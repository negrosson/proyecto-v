<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\SecurityAuditLog;
use App\Models\User;
use App\Rules\ThrottledCurrentPassword;
use DomainException;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var User $record */
        $oldIsAdmin = $record->is_admin;
        $oldIsActive = (bool) $record->is_active;
        $isAdmin = isset($data['is_admin']) ? (bool) $data['is_admin'] : $oldIsAdmin;
        unset($data['is_admin'], $data['password'], $data['password_confirmation'], $data['sudo_password']);

        // Bloquear que se desmarque el rol o estado activo de cuentas protegidas o propias
        if ($record->id === auth()->id() || $record->isDeveloper() || $record->email === 'admin@arriendoseventos.cl') {
            $isAdmin = true;
            $data['is_active'] = true;
        }

        $record->fill($data);
        $record->is_admin = $isAdmin;
        $record->save();

        if ($oldIsActive && ! $record->is_active) {
            UserResource::revokeUserSessions($record, preserveCurrentSession: false);
        }

        if ($oldIsAdmin !== $isAdmin) {
            SecurityAuditLog::record(
                eventType: 'user.privilege_changed',
                severity: 'critical',
                context: [
                    'target_user_id' => $record->id,
                    'email' => $record->email,
                    'old_is_admin' => $oldIsAdmin,
                    'new_is_admin' => $isAdmin,
                    'modified_by' => auth()->id(),
                ]
            );
        }

        return $record;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('resetPassword')
                ->label('Cambiar Clave')
                ->icon('heroicon-o-key')
                ->color('amber')
                ->form([
                    Forms\Components\TextInput::make('sudo_password')
                        ->label('Tu Contraseña Actual')
                        ->password()
                        ->revealable()
                        ->required()
                        ->rule(new ThrottledCurrentPassword),

                    Forms\Components\TextInput::make('new_password')
                        ->label('Nueva Contraseña')
                        ->password()
                        ->revealable()
                        ->required()
                        ->rule(Password::defaults())
                        ->same('new_password_confirmation'),

                    Forms\Components\TextInput::make('new_password_confirmation')
                        ->label('Confirmar Nueva Contraseña')
                        ->password()
                        ->revealable()
                        ->required(),

                    Forms\Components\Toggle::make('revoke_sessions')
                        ->label('Cerrar todas las sesiones activas de esta cuenta')
                        ->default(true),
                ])
                ->action(function (User $record, array $data): void {
                    $record->forceFill([
                        'password' => Hash::make((string) $data['new_password']),
                        'remember_token' => Str::random(60),
                    ])->save();

                    $revoked = ! empty($data['revoke_sessions'])
                        ? UserResource::revokeUserSessions($record, preserveCurrentSession: $record->id === auth()->id())
                        : 0;

                    SecurityAuditLog::record(
                        eventType: 'user.password_reset',
                        severity: 'critical',
                        context: [
                            'target_user_id' => $record->id,
                            'email' => $record->email,
                            'revoked_sessions' => $revoked,
                            'modified_by' => auth()->id(),
                        ]
                    );

                    Notification::make()
                        ->title('Contraseña actualizada con éxito')
                        ->success()
                        ->send();
                }),

            Actions\DeleteAction::make()
                ->hidden(fn (User $record): bool => $record->id === auth()->id() || $record->isDeveloper() || $record->email === 'admin@arriendoseventos.cl')
                ->form([
                    Forms\Components\TextInput::make('sudo_password')
                        ->label('Tu Contraseña Actual')
                        ->password()
                        ->revealable()
                        ->required()
                        ->rule(new ThrottledCurrentPassword),
                ])
                ->before(function (User $record): void {
                    if ($record->id === auth()->id()) {
                        throw new DomainException('No puedes eliminar tu propia cuenta de administrador.');
                    }

                    UserResource::revokeUserSessions($record, preserveCurrentSession: false);

                    SecurityAuditLog::record(
                        eventType: 'user.deleted',
                        severity: 'critical',
                        context: [
                            'deleted_user_id' => $record->id,
                            'email' => $record->email,
                            'deleted_by' => auth()->id(),
                        ]
                    );
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

<?php

declare(strict_types=1);

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Models\SecurityAuditLog;
use App\Models\User;
use DomainException;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        /** @var User $record */
        $oldIsAdmin = $record->is_admin;
        $isAdmin = isset($data['is_admin']) ? (bool) $data['is_admin'] : $oldIsAdmin;
        unset($data['is_admin']);

        // Bloquear que un administrador pueda desmarcar su propio rol de administrador
        if ($record->id === auth()->id() && ! $isAdmin) {
            $isAdmin = true;
        }

        $record->fill($data);
        $record->is_admin = $isAdmin;
        $record->save();

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
            Actions\DeleteAction::make()
                ->hidden(fn (User $record): bool => $record->id === auth()->id() || $record->email === 'admin@arriendoseventos.cl')
                ->before(function (User $record): void {
                    if ($record->id === auth()->id()) {
                        throw new DomainException('No puedes eliminar tu propia cuenta de administrador.');
                    }

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

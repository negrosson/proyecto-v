<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\SecurityAuditLog;
use App\Models\User;
use App\Rules\ThrottledCurrentPassword;
use DomainException;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Seguridad & Accesos';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Usuario';

    protected static ?string $pluralModelLabel = 'Usuarios y Administradores';

    /**
     * Oculta permanentemente las cuentas técnicas de desarrollador del listado y rutas de edición.
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('is_developer', false)
            ->where('email', '!=', 'dev@catalogo-arriendo.cl');
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) cache()->remember('filament_badge_users_count', 60, static fn () => (string) static::getEloquentQuery()->count());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identidad del Usuario')
                    ->description('Datos personales y correo corporativo de acceso al panel')
                    ->columns(['default' => 1, 'sm' => 2])
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre Completo')
                            ->required()
                            ->string()
                            ->minLength(2)
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                    ]),

                Forms\Components\Section::make('Permisos & Control de Acceso')
                    ->description('Privilegios administrativos, estado de cuenta y verificación de seguridad')
                    ->columns(['default' => 1, 'sm' => 2])
                    ->schema([
                        Forms\Components\Toggle::make('is_admin')
                            ->label('Privilegios de Administrador')
                            ->helperText('Permite el acceso y gestión del panel administrativo.')
                            ->default(true)
                            ->disabled(fn (?User $record): bool => $record !== null && ($record->id === auth()->id() || $record->isDeveloper() || $record->email === 'admin@arriendoseventos.cl'))
                            ->dehydrated(fn (?User $record): bool => $record === null || ($record->id !== auth()->id() && ! $record->isDeveloper() && $record->email !== 'admin@arriendoseventos.cl')),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Acceso Habilitado (Activo)')
                            ->helperText('Si se desactiva, el usuario será desconectado y bloqueado al instante.')
                            ->default(true)
                            ->disabled(fn (?User $record): bool => $record !== null && ($record->id === auth()->id() || $record->isDeveloper() || $record->email === 'admin@arriendoseventos.cl'))
                            ->dehydrated(fn (?User $record): bool => $record === null || ($record->id !== auth()->id() && ! $record->isDeveloper() && $record->email !== 'admin@arriendoseventos.cl')),

                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña de Acceso')
                            ->password()
                            ->revealable()
                            ->visibleOn('create')
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->rule(Password::defaults())
                            ->same('password_confirmation')
                            ->dehydrateStateUsing(fn ($state) => Hash::make((string) $state))
                            ->dehydrated(fn ($state, string $operation): bool => $operation === 'create' && filled($state))
                            ->helperText('Mínimo 10 caracteres con mayúsculas, minúsculas, números y símbolos.'),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Confirmar Contraseña')
                            ->password()
                            ->revealable()
                            ->visibleOn('create')
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->dehydrated(false),

                        Forms\Components\Placeholder::make('password_policy_notice')
                            ->label('Cambio de Contraseña Protegido')
                            ->visibleOn('edit')
                            ->columnSpanFull()
                            ->content('Por seguridad, la contraseña no se modifica desde aquí. Utiliza el botón «Cambiar Clave» en la tabla o en la barra superior, donde se validará tu contraseña actual y se revocarán las sesiones activas.'),

                        Forms\Components\TextInput::make('sudo_password')
                            ->label('Tu Contraseña Actual')
                            ->password()
                            ->revealable()
                            ->required()
                            ->rule(new ThrottledCurrentPassword)
                            ->dehydrated(false)
                            ->columnSpanFull()
                            ->helperText('Requerida para autorizar la creación o modificación de cuentas administrativas.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Usuario / Administrador')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (User $record): string => $record->email),

                Tables\Columns\TextColumn::make('is_admin')
                    ->label('Rol de Acceso')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Super Administrador' : 'Operador')
                    ->color(fn (bool $state): string => $state ? 'info' : 'gray')
                    ->icon(fn (bool $state): string => $state ? 'heroicon-m-shield-check' : 'heroicon-m-user')
                    ->sortable(),

                Tables\Columns\TextColumn::make('two_factor_confirmed_at')
                    ->label('Protección 2FA')
                    ->badge()
                    ->getStateUsing(fn (User $record): string => $record->hasEnabledTwoFactor() ? '2FA Activo' : 'Sin 2FA')
                    ->color(fn (User $record): string => $record->hasEnabledTwoFactor() ? 'success' : 'warning')
                    ->icon(fn (User $record): string => $record->hasEnabledTwoFactor() ? 'heroicon-m-lock-closed' : 'heroicon-m-exclamation-triangle'),

                Tables\Columns\TextColumn::make('is_active')
                    ->label('Estado')
                    ->badge()
                    ->getStateUsing(function (User $record): string {
                        if (! $record->is_active) {
                            return 'Suspendido';
                        }

                        if ($record->isMfaLocked()) {
                            return 'Bloqueado 2FA';
                        }

                        return 'Activo';
                    })
                    ->color(function (User $record): string {
                        if (! $record->is_active) {
                            return 'danger';
                        }

                        if ($record->isMfaLocked()) {
                            return 'warning';
                        }

                        return 'success';
                    })
                    ->icon(function (User $record): string {
                        if (! $record->is_active) {
                            return 'heroicon-m-no-symbol';
                        }

                        if ($record->isMfaLocked()) {
                            return 'heroicon-m-clock';
                        }

                        return 'heroicon-m-check-circle';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('active_sessions')
                    ->label('Sesiones')
                    ->badge()
                    ->getStateUsing(function (User $record): string {
                        $count = DB::table('sessions')
                            ->where('user_id', $record->id)
                            ->count();

                        return $count === 1 ? '1 activa' : "{$count} activas";
                    })
                    ->color(function (User $record): string {
                        $count = DB::table('sessions')
                            ->where('user_id', $record->id)
                            ->count();

                        return $count > 1 ? 'warning' : ($count === 1 ? 'success' : 'gray');
                    })
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Miembro Desde')
                    ->dateTime('d/m/Y')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'asc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_admin')
                    ->label('Filtrar por Rol')
                    ->trueLabel('Solo Administradores')
                    ->falseLabel('Solo Operadores'),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Filtrar por Estado')
                    ->trueLabel('Solo Cuentas Activas')
                    ->falseLabel('Solo Suspendidas'),

                Tables\Filters\TernaryFilter::make('two_factor_confirmed_at')
                    ->label('Protección 2FA')
                    ->nullable()
                    ->trueLabel('Con 2FA Activo')
                    ->falseLabel('Sin 2FA'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->slideOver(),

                // Acción Blindada Modo Sudo: Cambiar Contraseña con Confirmación y Revocación de Sesiones
                Tables\Actions\Action::make('resetPassword')
                    ->label('Cambiar Clave')
                    ->icon('heroicon-o-key')
                    ->color('amber')
                    ->form([
                        Forms\Components\TextInput::make('sudo_password')
                            ->label('Tu Contraseña Actual')
                            ->password()
                            ->revealable()
                            ->required()
                            ->rule(new ThrottledCurrentPassword)
                            ->helperText('Debes confirmar tu propia contraseña para autorizar este cambio.'),

                        Forms\Components\TextInput::make('new_password')
                            ->label('Nueva Contraseña')
                            ->password()
                            ->revealable()
                            ->required()
                            ->rule(Password::defaults())
                            ->same('new_password_confirmation')
                            ->helperText('Mínimo 10 caracteres con mayúsculas, minúsculas, números y símbolos.'),

                        Forms\Components\TextInput::make('new_password_confirmation')
                            ->label('Confirmar Nueva Contraseña')
                            ->password()
                            ->revealable()
                            ->required(),

                        Forms\Components\Toggle::make('revoke_sessions')
                            ->label('Cerrar todas las sesiones activas de esta cuenta')
                            ->helperText('Expulsa inmediatamente cualquier dispositivo conectado con la clave anterior.')
                            ->default(true),
                    ])
                    ->modalHeading(fn (User $record): string => "Cambio Seguro de Contraseña: {$record->name}")
                    ->modalDescription('Esta operación requiere verificación de tu contraseña actual y queda registrada en la auditoría de seguridad.')
                    ->modalSubmitActionLabel('Confirmar y Actualizar Clave')
                    ->action(function (User $record, array $data): void {
                        $record->forceFill([
                            'password' => Hash::make((string) $data['new_password']),
                            'remember_token' => Str::random(60),
                        ])->save();

                        $revokedSessions = 0;
                        if (! empty($data['revoke_sessions'])) {
                            $revokedSessions = self::revokeUserSessions($record, preserveCurrentSession: $record->id === auth()->id());
                        }

                        SecurityAuditLog::record(
                            eventType: 'user.password_reset',
                            severity: 'critical',
                            context: [
                                'target_user_id' => $record->id,
                                'email' => $record->email,
                                'revoked_sessions' => $revokedSessions,
                                'modified_by' => auth()->id(),
                            ]
                        );

                        Notification::make()
                            ->title('Contraseña actualizada con éxito')
                            ->body($revokedSessions > 0
                                ? "Clave cambiada y {$revokedSessions} sesión(es) activa(s) revocadas."
                                : 'Clave cambiada y registrada en auditoría forense.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\ActionGroup::make([
                    // Revocar todas las sesiones activas
                    Tables\Actions\Action::make('revokeSessions')
                        ->label('Cerrar Sesiones Activas')
                        ->icon('heroicon-o-arrow-right-on-rectangle')
                        ->color('warning')
                        ->form([
                            Forms\Components\TextInput::make('sudo_password')
                                ->label('Tu Contraseña Actual')
                                ->password()
                                ->revealable()
                                ->required()
                                ->rule(new ThrottledCurrentPassword),
                        ])
                        ->modalHeading(fn (User $record): string => "Cerrar sesiones activas de {$record->name}")
                        ->modalDescription('Desconectará inmediatamente a este usuario de todos los navegadores y dispositivos.')
                        ->modalSubmitActionLabel('Cerrar Todas las Sesiones')
                        ->action(function (User $record): void {
                            $count = self::revokeUserSessions($record, preserveCurrentSession: $record->id === auth()->id());

                            SecurityAuditLog::record(
                                eventType: 'user.sessions_revoked',
                                severity: 'warning',
                                context: [
                                    'target_user_id' => $record->id,
                                    'email' => $record->email,
                                    'sessions_revoked' => $count,
                                    'modified_by' => auth()->id(),
                                ]
                            );

                            Notification::make()
                                ->title("Sesiones revocadas ({$count})")
                                ->success()
                                ->send();
                        }),

                    // Restablecer 2FA / Desbloquear MFA de otro usuario (con Modo Sudo)
                    Tables\Actions\Action::make('resetMfa')
                        ->label('Restablecer / Desbloquear 2FA')
                        ->icon('heroicon-o-shield-exclamation')
                        ->color('danger')
                        ->visible(fn (User $record): bool => $record->id !== auth()->id() && ($record->hasEnabledTwoFactor() || $record->isMfaLocked()))
                        ->form([
                            Forms\Components\TextInput::make('sudo_password')
                                ->label('Tu Contraseña Actual')
                                ->password()
                                ->revealable()
                                ->required()
                                ->rule(new ThrottledCurrentPassword),
                        ])
                        ->modalHeading(fn (User $record): string => "Restablecer 2FA de {$record->name}")
                        ->modalDescription('Eliminará el 2FA y cualquier bloqueo temporal de esta cuenta, cerrando sus sesiones activas. Úsalo solo si el usuario perdió su dispositivo.')
                        ->modalSubmitActionLabel('Confirmar Restablecimiento 2FA')
                        ->action(function (User $record): void {
                            $record->forceFill([
                                'two_factor_secret' => null,
                                'two_factor_recovery_codes' => null,
                                'two_factor_confirmed_at' => null,
                                'last_totp_slice' => null,
                                'mfa_locked_until' => null,
                                'remember_token' => Str::random(60),
                            ])->save();

                            self::revokeUserSessions($record, preserveCurrentSession: false);

                            SecurityAuditLog::record(
                                eventType: 'user.mfa_admin_reset',
                                severity: 'critical',
                                context: [
                                    'target_user_id' => $record->id,
                                    'email' => $record->email,
                                    'modified_by' => auth()->id(),
                                ]
                            );

                            Notification::make()
                                ->title('2FA restablecido y sesiones revocadas')
                                ->warning()
                                ->send();
                        }),

                    // Suspender / Reactivar acceso (con Modo Sudo)
                    Tables\Actions\Action::make('toggleStatus')
                        ->label(fn (User $record): string => $record->is_active ? 'Suspender Cuenta' : 'Reactivar Cuenta')
                        ->icon(fn (User $record): string => $record->is_active ? 'heroicon-o-no-symbol' : 'heroicon-o-check')
                        ->color(fn (User $record): string => $record->is_active ? 'danger' : 'success')
                        ->hidden(fn (User $record): bool => $record->id === auth()->id() || $record->email === 'admin@arriendoseventos.cl' || $record->isDeveloper())
                        ->form([
                            Forms\Components\TextInput::make('sudo_password')
                                ->label('Tu Contraseña Actual')
                                ->password()
                                ->revealable()
                                ->required()
                                ->rule(new ThrottledCurrentPassword),
                        ])
                        ->modalHeading(fn (User $record): string => $record->is_active ? "Suspender acceso a {$record->name}" : "Reactivar acceso a {$record->name}")
                        ->modalDescription(fn (User $record): string => $record->is_active
                            ? 'Al suspender la cuenta, todas sus sesiones serán cerradas de inmediato y no podrá volver a iniciar sesión.'
                            : 'El usuario recuperará el acceso al panel con sus credenciales actuales.')
                        ->action(function (User $record): void {
                            $newState = ! $record->is_active;
                            $record->forceFill([
                                'is_active' => $newState,
                                'remember_token' => Str::random(60),
                            ])->save();

                            if (! $newState) {
                                self::revokeUserSessions($record, preserveCurrentSession: false);
                            }

                            SecurityAuditLog::record(
                                eventType: 'user.status_toggled',
                                severity: 'critical',
                                context: [
                                    'target_user_id' => $record->id,
                                    'email' => $record->email,
                                    'new_is_active' => $newState,
                                    'modified_by' => auth()->id(),
                                ]
                            );

                            Notification::make()
                                ->title($newState ? 'Cuenta reactivada' : 'Cuenta suspendida y desconectada')
                                ->status($newState ? 'success' : 'warning')
                                ->send();
                        }),

                    // Eliminación definitiva (con Modo Sudo)
                    Tables\Actions\DeleteAction::make()
                        ->hidden(fn (User $record): bool => $record->id === auth()->id() || $record->email === 'admin@arriendoseventos.cl' || $record->isDeveloper())
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

                            self::revokeUserSessions($record, preserveCurrentSession: false);

                            SecurityAuditLog::record(
                                eventType: 'user.deleted',
                                severity: 'critical',
                                context: [
                                    'deleted_user_id' => $record->id,
                                    'email' => $record->email,
                                    'deleted_by' => auth()->id(),
                                ]
                            );
                        })
                        ->modalHeading('¿Eliminar cuenta definitivamente?')
                        ->modalDescription('Acción irreversible. Si solo quieres revocar el acceso temporalmente, usa «Suspender Cuenta».'),
                ])
                    ->label('Controles')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->color('gray'),
            ])
            ->bulkActions([]);
    }

    /**
     * Revoca todas las sesiones persistidas de un usuario en la tabla `sessions`.
     */
    public static function revokeUserSessions(User $user, bool $preserveCurrentSession = false): int
    {
        $query = DB::table('sessions')->where('user_id', $user->id);

        if ($preserveCurrentSession && session()->getId() !== '') {
            $query->where('id', '!=', session()->getId());
        }

        return $query->delete();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

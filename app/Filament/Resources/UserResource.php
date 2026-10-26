<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\SecurityAuditLog;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationGroup = 'Seguridad & Accesos';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Usuario';

    protected static ?string $pluralModelLabel = 'Usuarios y Administradores';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identidad del Usuario')
                    ->description('Datos personales y de contacto para el acceso corporativo')
                    ->columns(['default' => 1, 'sm' => 2])
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nombre Completo')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('Correo Electrónico')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                    ]),

                Forms\Components\Section::make('Permisos de Seguridad & Credenciales (OWASP A01/A07)')
                    ->description('Gestión de privilegios administrativos y control de estado de la cuenta')
                    ->columns(['default' => 1, 'sm' => 2])
                    ->schema([
                        Forms\Components\Toggle::make('is_admin')
                            ->label('Privilegios de Administrador')
                            ->helperText('Permite el acceso y gestión total del panel administrativo de Filament.')
                            ->disabled(fn (?User $record): bool => $record !== null && $record->id === auth()->id())
                            ->dehydrated(fn (?User $record): bool => $record === null || $record->id !== auth()->id()),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Acceso Habilitado (Activo)')
                            ->helperText('Si se desactiva, el usuario no podrá iniciar sesión en ninguna parte del sistema.')
                            ->default(true)
                            ->disabled(fn (?User $record): bool => $record !== null && $record->id === auth()->id())
                            ->dehydrated(fn (?User $record): bool => $record === null || $record->id !== auth()->id()),

                        Forms\Components\TextInput::make('password')
                            ->label('Contraseña de Acceso')
                            ->password()
                            ->revealable()
                            ->columnSpanFull()
                            ->helperText('Mínimo 10 caracteres, combinando mayúsculas, minúsculas, números y símbolos. Dejar vacío en edición si no se desea modificar.')
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->rule(Password::defaults())
                            ->dehydrateStateUsing(fn ($state) => Hash::make((string) $state))
                            ->dehydrated(fn ($state) => filled($state)),
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
                    ->formatStateUsing(function (bool $state, User $record): string {
                        if ($record->isDeveloper()) {
                            return 'Desarrollador';
                        }

                        return $state ? 'Super Administrador' : 'Operador';
                    })
                    ->color(function (bool $state, User $record): string {
                        if ($record->isDeveloper()) {
                            return 'warning';
                        }

                        return $state ? 'info' : 'gray';
                    })
                    ->icon(function (bool $state, User $record): string {
                        if ($record->isDeveloper()) {
                            return 'heroicon-m-command-line';
                        }

                        return $state ? 'heroicon-m-shield-check' : 'heroicon-m-user';
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('is_active')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Activo' : 'Suspendido')
                    ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                    ->icon(fn (bool $state): string => $state ? 'heroicon-m-check-circle' : 'heroicon-m-no-symbol')
                    ->sortable(),

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
            ])
            ->actions([
                Tables\Actions\EditAction::make()->slideOver(),

                // Acción profesional: Restablecer Contraseña
                Tables\Actions\Action::make('resetPassword')
                    ->label('Contraseña')
                    ->icon('heroicon-o-key')
                    ->color('gray')
                    ->form([
                        Forms\Components\TextInput::make('new_password')
                            ->label('Nueva Contraseña')
                            ->password()
                            ->revealable()
                            ->required()
                            ->rule(Password::defaults())
                            ->helperText('Mínimo 10 caracteres con mayúsculas, minúsculas, números y símbolos (OWASP).'),
                    ])
                    ->modalHeading(fn (User $record): string => "Restablecer Contraseña: {$record->name}")
                    ->modalDescription('Asigna una nueva clave de acceso seguro. El usuario podrá utilizarla de inmediato.')
                    ->modalSubmitActionLabel('Actualizar Contraseña')
                    ->action(function (User $record, array $data): void {
                        $record->update([
                            'password' => Hash::make((string) $data['new_password']),
                        ]);

                        Notification::make()
                            ->title('Contraseña actualizada con éxito')
                            ->success()
                            ->send();
                    }),

                // Acción profesional: Suspender / Reactivar acceso
                Tables\Actions\Action::make('toggleStatus')
                    ->label(fn (User $record): string => $record->is_active ? 'Suspender' : 'Activar')
                    ->icon(fn (User $record): string => $record->is_active ? 'heroicon-o-no-symbol' : 'heroicon-o-check')
                    ->color(fn (User $record): string => $record->is_active ? 'danger' : 'success')
                    ->hidden(fn (User $record): bool => $record->id === auth()->id() || $record->email === 'admin@arriendoseventos.cl' || $record->email === 'dev@catalogo-arriendo.cl')
                    ->requiresConfirmation()
                    ->modalHeading(fn (User $record): string => $record->is_active ? "Suspender acceso a {$record->name}" : "Reactivar acceso a {$record->name}")
                    ->modalDescription(fn (User $record): string => $record->is_active
                        ? 'Al suspender la cuenta, el usuario será desconectado y no podrá volver a iniciar sesión hasta que sea reactivado.'
                        : 'El usuario recuperará el acceso inmediato al sistema con sus credenciales habituales.')
                    ->action(function (User $record): void {
                        $record->update(['is_active' => ! $record->is_active]);

                        Notification::make()
                            ->title($record->is_active ? 'Cuenta reactivada' : 'Cuenta suspendida correctamente')
                            ->status($record->is_active ? 'success' : 'warning')
                            ->send();
                    }),

                // Acción de eliminación protegida
                Tables\Actions\DeleteAction::make()
                    ->hidden(fn (User $record): bool => $record->id === auth()->id() || $record->email === 'admin@arriendoseventos.cl' || $record->email === 'dev@catalogo-arriendo.cl')
                    ->before(function (User $record): void {
                        if ($record->id === auth()->id()) {
                            throw new \DomainException('No puedes eliminar tu propia cuenta de administrador.');
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
                    })
                    ->modalHeading('¿Eliminar cuenta definitivamente?')
                    ->modalDescription('Esta acción es irreversible y removerá las credenciales permanentemente. Se recomienda "Suspender" si solo deseas revocar el acceso temporalmente.'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->action(function ($records): void {
                            $records->filter(fn (User $user) => $user->id !== auth()->id() && $user->email !== 'admin@arriendoseventos.cl' && $user->email !== 'dev@catalogo-arriendo.cl')->each->delete();
                        }),
                ]),
            ]);
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

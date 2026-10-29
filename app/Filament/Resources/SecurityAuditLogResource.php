<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\SecurityAuditLogResource\Pages;
use App\Models\SecurityAuditLog;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * Recurso de Filament para la visualización forense de registros inmutables de auditoría (OWASP A09).
 *
 * Estrictamente de solo lectura: no permite crear, editar ni eliminar registros.
 */
class SecurityAuditLogResource extends Resource
{
    protected static ?string $model = SecurityAuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-finger-print';

    protected static ?string $navigationGroup = 'Seguridad & Accesos';

    public static function getNavigationLabel(): string
    {
        /** @var User|null $user */
        $user = auth()->user();

        return ($user && method_exists($user, 'isDeveloper') && $user->isDeveloper())
            ? 'Auditoría'
            : 'Auditoría Forense & Logs';
    }

    public static function getPluralModelLabel(): string
    {
        /** @var User|null $user */
        $user = auth()->user();

        return ($user && method_exists($user, 'isDeveloper') && $user->isDeveloper())
            ? 'Auditoría'
            : 'Auditoría Forense & Logs';
    }

    public static function getBreadcrumb(): string
    {
        return static::getNavigationLabel();
    }

    public static function canAccess(): bool
    {
        /** @var User|null $user */
        $user = auth()->user();

        return (bool) ($user && method_exists($user, 'isDeveloper') && $user->isDeveloper());
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function canViewAny(): bool
    {
        return static::canAccess();
    }

    public static function canView(Model $record): bool
    {
        return static::canAccess();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(fn (): string => (auth()->user()?->isDeveloper() ? 'Detalle de Auditoría' : 'Detalle del Evento Forense'))
                    ->columns(['default' => 1, 'sm' => 2])
                    ->schema([
                        Forms\Components\TextInput::make('created_at')
                            ->label('Fecha & Hora (ISO 8601)')
                            ->disabled(),

                        Forms\Components\TextInput::make('event_type')
                            ->label('Tipo de Evento')
                            ->disabled(),

                        Forms\Components\TextInput::make('severity')
                            ->label('Nivel de Severidad')
                            ->disabled(),

                        Forms\Components\TextInput::make('ip_address')
                            ->label('Dirección IP de Origen')
                            ->disabled(),

                        Forms\Components\Textarea::make('user_agent')
                            ->label('User Agent del Navegador')
                            ->rows(2)
                            ->disabled()
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('context')
                            ->label('Carga Útil / Contexto')
                            ->rows(4)
                            ->formatStateUsing(fn ($state): string => is_array($state) ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : (string) $state)
                            ->disabled()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('previous_hash')
                            ->label('Hash Previo (Cadena Criptográfica)')
                            ->disabled()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('signature_hmac')
                            ->label('Firma Digital HMAC-SHA256')
                            ->disabled()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha & Hora')
                    ->dateTime('d/m/Y H:i:s')
                    ->since()
                    ->sortable(),

                Tables\Columns\TextColumn::make('event_type')
                    ->label('Evento')
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('severity')
                    ->label('Severidad')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => strtoupper($state))
                    ->color(fn (string $state): string => match ($state) {
                        'critical' => 'danger',
                        'warning' => 'warning',
                        default => 'info',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'critical' => 'heroicon-m-shield-exclamation',
                        'warning' => 'heroicon-m-exclamation-triangle',
                        default => 'heroicon-m-information-circle',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Usuario / IP')
                    ->default('Sistema / Anónimo')
                    ->description(fn (SecurityAuditLog $record): string => $record->ip_address)
                    ->searchable(),

                // Integridad HMAC en tiempo real
                Tables\Columns\TextColumn::make('hmac_integrity')
                    ->label('Integridad HMAC')
                    ->badge()
                    ->state(fn (SecurityAuditLog $record): string => $record->verifyIntegrity() ? 'Firma Válida' : 'ADULTERADO')
                    ->color(fn (string $state): string => $state === 'Firma Válida' ? 'success' : 'danger')
                    ->icon(fn (string $state): string => $state === 'Firma Válida' ? 'heroicon-m-check-badge' : 'heroicon-m-exclamation-triangle'),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('severity')
                    ->label('Filtrar por Severidad')
                    ->options([
                        'info' => 'Info',
                        'warning' => 'Warning',
                        'critical' => 'Critical',
                    ]),

                Tables\Filters\SelectFilter::make('event_type')
                    ->label('Filtrar por Evento')
                    ->options([
                        'mfa.enabled' => 'MFA Activado',
                        'mfa.disabled' => 'MFA Desactivado',
                        'mfa.challenge_success' => 'Desafío MFA Exitoso',
                        'mfa.challenge_failed' => 'Desafío MFA Fallido',
                        'mfa.circuit_breaker_triggered' => 'Bloqueo Temporal (Circuit Breaker)',
                        'user.created' => 'Usuario Creado',
                        'user.privilege_changed' => 'Privilegio Modificado',
                        'user.deleted' => 'Usuario Eliminado',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->slideOver(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSecurityAuditLogs::route('/'),
        ];
    }
}

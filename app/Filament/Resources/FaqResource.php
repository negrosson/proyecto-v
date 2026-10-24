<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = 'Contenido & Vitrina Web';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Pregunta Frecuente';

    protected static ?string $pluralModelLabel = 'Preguntas Frecuentes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('question')
                    ->label('Pregunta')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Select::make('category')
                    ->label('Categoría')
                    ->options([
                        'Reservas y Pagos' => 'Reservas y Pagos',
                        'Vajilla y Lavado' => 'Vajilla y Lavado',
                        'Garantías y Roturas' => 'Garantías y Roturas',
                        'Despacho y Montaje' => 'Despacho y Montaje',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('sort_order')
                    ->label('Orden de Visualización')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label('Visible en la Web')
                    ->default(true),
                Forms\Components\Textarea::make('answer')
                    ->label('Respuesta Detallada')
                    ->required()
                    ->rows(4)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question')
                    ->label('Pregunta')
                    ->searchable()
                    ->limit(60),
                Tables\Columns\TextColumn::make('category')
                    ->label('Categoría')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Orden')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Activa')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Actualizado')
                    ->dateTime('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->label('Categoría')
                    ->options([
                        'Reservas y Pagos' => 'Reservas y Pagos',
                        'Vajilla y Lavado' => 'Vajilla y Lavado',
                        'Garantías y Roturas' => 'Garantías y Roturas',
                        'Despacho y Montaje' => 'Despacho y Montaje',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}

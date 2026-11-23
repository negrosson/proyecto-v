<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\SiteSettingResource\Pages;
use App\Models\SiteSetting;
use App\Services\Media\PublicMediaCleaner;
use App\Services\Security\SafeImageSanitizer;
use App\Support\Filament\PreviewImageResolver;
use Filament\Forms;
use Filament\Forms\Components\BaseFileUpload;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;

class SiteSettingResource extends Resource
{
    protected static ?string $model = SiteSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Contenido & Vitrina Web';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Configuración del Sitio';

    protected static ?string $pluralModelLabel = 'Configuración del Sitio';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('CMS_Settings')
                    ->tabs([
                        // Pestaña 1: Identidad & Hero
                        Forms\Components\Tabs\Tab::make('Identidad & Hero')
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                Forms\Components\TextInput::make('site_name')
                                    ->label('Nombre del Sitio / Marca')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('site_tagline')
                                    ->label('Lema / Tagline')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(150),
                                Forms\Components\FileUpload::make('logo_path')
                                    ->label('Logo Oficial (Opcional)')
                                    ->disk('public')
                                    ->directory('site')
                                    ->visibility('public')
                                    ->image()
                                    ->live()
                                    ->maxSize(4096)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->deletable(true)
                                    ->openable()
                                    ->downloadable()
                                    ->nullable()
                                    ->helperText('Formato PNG, WebP o JPG. Tamaño máx: 4 MB. Si lo dejas vacío se muestra el logo vectorial oficial VYS.')
                                    ->saveUploadedFileUsing(fn (UploadedFile $file, BaseFileUpload $component): string => SafeImageSanitizer::sanitizeForFilament($file, 'public', 'site', $component->getStatePath()))
                                    ->deleteUploadedFileUsing(function (mixed $file): void {
                                        if (is_string($file)) {
                                            PublicMediaCleaner::deleteIfUnreferenced($file);
                                        }
                                    }),
                                Forms\Components\TextInput::make('hero_badge')
                                    ->label('Badge Superior del Hero')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('hero_title')
                                    ->label('Título Principal del Hero')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(150),
                                Forms\Components\Textarea::make('hero_subtitle')
                                    ->label('Subtítulo y Propuesta de Valor')
                                    ->required()
                                    ->live(onBlur: true)
                                    ->rows(3)
                                    ->columnSpanFull(),
                                Forms\Components\FileUpload::make('hero_bg_image')
                                    ->label('Imagen de Fondo del Hero (Opcional)')
                                    ->disk('public')
                                    ->directory('site')
                                    ->visibility('public')
                                    ->image()
                                    ->live()
                                    ->maxSize(4096)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->deletable(true)
                                    ->openable()
                                    ->downloadable()
                                    ->nullable()
                                    ->helperText('Fotografía de fondo (JPG, PNG, WebP). Tamaño máx: 4 MB. Al subirla verás abajo cómo queda aplicada.')
                                    ->saveUploadedFileUsing(fn (UploadedFile $file, BaseFileUpload $component): string => SafeImageSanitizer::sanitizeForFilament($file, 'public', 'site', $component->getStatePath()))
                                    ->deleteUploadedFileUsing(function (mixed $file): void {
                                        if (is_string($file)) {
                                            PublicMediaCleaner::deleteIfUnreferenced($file);
                                        }
                                    })
                                    ->columnSpanFull(),

                                Forms\Components\Placeholder::make('hero_live_preview')
                                    ->label('Vista Previa en Tiempo Real (Header & Hero)')
                                    ->content(fn (Get $get): View => view('filament.previews.site-hero-preview', [
                                        'siteName' => (string) ($get('site_name') ?? ''),
                                        'siteTagline' => (string) ($get('site_tagline') ?? ''),
                                        'logoUrl' => PreviewImageResolver::resolveSingle($get('logo_path')),
                                        'heroBadge' => (string) ($get('hero_badge') ?? ''),
                                        'heroTitle' => (string) ($get('hero_title') ?? ''),
                                        'heroSubtitle' => (string) ($get('hero_subtitle') ?? ''),
                                        'heroBgUrl' => PreviewImageResolver::resolveSingle($get('hero_bg_image')),
                                    ]))
                                    ->columnSpanFull(),
                            ])->columns(['default' => 1, 'md' => 2]),

                        // Pestaña 2: Empresa, Mapa & Redes
                        Forms\Components\Tabs\Tab::make('Empresa, Mapa & Redes')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Forms\Components\TextInput::make('company_address')
                                    ->label('Dirección Comercial / Bodega')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('company_phone')
                                    ->label('Teléfono de Contacto')
                                    ->required()
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('whatsapp_phone')
                                    ->label('Teléfono WhatsApp (Ej: 56912345678)')
                                    ->required()
                                    ->maxLength(50),
                                Forms\Components\TextInput::make('company_email')
                                    ->label('Correo Electrónico Oficial')
                                    ->email()
                                    ->required()
                                    ->maxLength(100),
                                Forms\Components\TextInput::make('business_hours')
                                    ->label('Horarios de Atención y Despacho')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('company_legal_info')
                                    ->label('Razón Social / Información Legal')
                                    ->rows(2)
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('map_embed_url')
                                    ->label('URL de Google Maps Embebido (iframe src)')
                                    ->required()
                                    ->rows(3)
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('instagram_url')
                                    ->label('URL Instagram')
                                    ->placeholder('https://instagram.com/tu_cuenta')
                                    ->helperText('Opcional. Si lo dejas vacío, el ícono no se mostrará en la web.')
                                    ->nullable()
                                    ->url()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('facebook_url')
                                    ->label('URL Facebook')
                                    ->placeholder('https://facebook.com/tu_pagina')
                                    ->helperText('Opcional. Si lo dejas vacío, el ícono no se mostrará en la web.')
                                    ->nullable()
                                    ->url()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('tiktok_url')
                                    ->label('URL TikTok')
                                    ->placeholder('https://tiktok.com/@tu_cuenta')
                                    ->helperText('Opcional. Si lo dejas vacío, el ícono no se mostrará en la web.')
                                    ->nullable()
                                    ->url()
                                    ->maxLength(255),
                            ])->columns(['default' => 1, 'md' => 2]),

                        // Pestaña 3: Políticas de Envíos
                        Forms\Components\Tabs\Tab::make('Políticas de Envíos')
                            ->icon('heroicon-o-truck')
                            ->schema([
                                Forms\Components\TextInput::make('shipping_title')
                                    ->label('Título de la Sección de Envíos')
                                    ->required()
                                    ->maxLength(150)
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('shipping_subtitle')
                                    ->label('Subtítulo de la Sección')
                                    ->required()
                                    ->rows(2)
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('shipping_zones_info')
                                    ->label('1. Tarifa según Comuna y Volumen')
                                    ->required()
                                    ->rows(3),
                                Forms\Components\Textarea::make('shipping_unload_rules')
                                    ->label('2. Condiciones de Descarga (Nivel de Calle)')
                                    ->required()
                                    ->rows(3),
                                Forms\Components\Textarea::make('shipping_setup_rules')
                                    ->label('3. Instalación Técnica Obligatoria (Carpas/Gas)')
                                    ->required()
                                    ->rows(3),
                                Forms\Components\Textarea::make('shipping_schedule_rules')
                                    ->label('4. Ventanas Horarias y Conteo con Guía')
                                    ->required()
                                    ->rows(3),
                            ])->columns(['default' => 1, 'md' => 2]),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('site_name')
                    ->label('Nombre del Sitio')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('whatsapp_phone')
                    ->label('WhatsApp')
                    ->searchable(),
                Tables\Columns\TextColumn::make('company_email')
                    ->label('Email')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSiteSettings::route('/'),
            'edit' => Pages\EditSiteSetting::route('/{record}/edit'),
        ];
    }
}

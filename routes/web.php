<?php

declare(strict_types=1);

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\QuoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/montajes', [CatalogController::class, 'montajes'])->name('catalog.montajes');
Route::get('/catalogo/buscar', [CatalogController::class, 'search'])
    ->name('catalog.search')
    ->middleware('throttle:catalog-search');

Route::post('/catalogo/productos/{product}/click', [CatalogController::class, 'trackClick'])
    ->name('catalog.track-click')
    ->middleware('throttle:product-telemetry');

Route::post('/cotizacion', [QuoteController::class, 'store'])
    ->name('quote.store')
    ->middleware('throttle:quote-submission');

Route::post('/contacto', [ContactController::class, 'store'])
    ->name('contact.store')
    ->middleware('throttle:contact-submission');

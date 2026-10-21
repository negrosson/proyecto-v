<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\QuoteController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'index'])->name('catalog.index');
Route::post('/cotizacion', [QuoteController::class, 'store'])->name('quote.store');

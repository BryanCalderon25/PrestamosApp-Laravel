<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrestamoController;

Route::get('/', fn() => redirect('/prestamos/crear'));

Route::get('/prestamos/crear', [PrestamoController::class, 'crear'])->name('prestamos.crear');
Route::post('/prestamos/procesar', [PrestamoController::class, 'procesar'])->name('prestamos.procesar');
Route::get('/prestamos/aprobados', [PrestamoController::class, 'aprobados'])->name('prestamos.aprobados');

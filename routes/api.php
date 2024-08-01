<?php

use App\Http\Controllers\CalculationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/calculate', [CalculationController::class, 'calculate']);
Route::get('/recalculation/{id}', [CalculationController::class, 'recalculation']);
Route::get('/calculations', [CalculationController::class, 'index']);

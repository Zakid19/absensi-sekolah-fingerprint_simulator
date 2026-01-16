<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FingerprintController;

Route::post('/fingerprint/scan', [FingerprintController::class, 'scan']);
Route::post('/fingerprint/sync', [FingerprintController::class, 'sync']);


// Route::post('/fingerprint/log', [FingerprintController::class, 'scan']);

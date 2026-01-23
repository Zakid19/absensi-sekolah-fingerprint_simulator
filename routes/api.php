<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\Api\FingerprintRegisterController;
use App\Http\Controllers\PendingFingerprintController;
use App\Http\Controllers\FingerprintAttendanceController;


// Device
Route::post('/fingerprint/push', [FingerprintController::class, 'push']);
Route::get('/fingerprint/last', [PendingFingerprintController::class, 'last']);
Route::get('/attendance/last', [FingerprintAttendanceController::class,'last']);


// Simulator
Route::post('/fingerprint/scan', [FingerprintController::class, 'scan']);
// Route::post('/fingerprint/sync', [FingerprintController::class, 'sync']);

Route::post('/fingerprint/log', [FingerprintLogController::class,
    'receive'
]);


// Route::post('/fingerprint/log', [FingerprintController::class, 'scan']);

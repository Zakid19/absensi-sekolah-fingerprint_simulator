<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\FingerprintController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SystemBackupController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\DeviceFingerprintController;
use App\Http\Controllers\AttendanceSettingController;
use App\Http\Controllers\PendingFingerprintController;
use App\Http\Controllers\FingerprintModeController;

Route::get('/', function () {
    return view('auth.login');
});

// Route::get('/', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');


// Route::middleware(['auth', 'role:admin'])->group(function () {
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::group(['prefix' => 'class', 'as' => 'class.'], function() {
        Route::get('/manage', [ClassRoomController::class, 'manage'])->name('manage');
        Route::get('/data', [ClassRoomController::class, 'getData'])->name('data');
        Route::get('/create', [ClassRoomController::class, 'create'])->name('create');
        Route::post('/store', [ClassRoomController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [ClassRoomController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [ClassRoomController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ClassRoomController::class, 'delete'])->name('delete');
        Route::get('/', [ClassRoomController::class, 'index'])->name('index');
        Route::get('/{classRoom}', [ClassRoomController::class, 'show'])->name('show');
    });

    Route::group(['prefix' => 'student', 'as' => 'student.'], function () {
        Route::get('/data', [StudentController::class, 'getData'])->name('data');
        Route::get('/create', [StudentController::class, 'create'])->name('create');
        Route::post('/store', [StudentController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [StudentController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [StudentController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [StudentController::class, 'delete'])->name('delete');
        Route::get('/{classRoom}', [StudentController::class, 'manage'])->name('manage');
    });

    Route::group(['prefix' => 'attendance', 'as' => 'attendance.'], function() {
        Route::get('/manage', [AttendanceController::class, 'manage'])->name('manage');
        Route::get('/data', [AttendanceController::class, 'getData'])->name('data');
        Route::delete('/delete/{id}', [AttendanceController::class, 'delete'])->name('delete');
        Route::get('/history/{student}', [AttendanceController::class, 'history'])->name('history');

    });

    Route::prefix('attendance')->name('attendance.')->group(function () {
        Route::get('/settings', [AttendanceSettingController::class, 'edit'])->name('settings.edit');
        Route::post('/settings', [AttendanceSettingController::class, 'update'])->name('settings.update');
    });

    Route::group(['prefix' => 'reports', 'as' => 'reports.'], function() {
        Route::get('/manage', [ReportController::class, 'manage'])->name('manage');
        Route::get('/data', [ReportController::class, 'getData'])->name('data');
        Route::get('/export/excel', [ReportController::class, 'exportExcel']);
        Route::get('/export/pdf', [ReportController::class, 'exportPdf']);
    });

    Route::prefix('system')->group(function () {
        Route::get('/backup', [SystemBackupController::class, 'index'])->name('backup.index');
        Route::post('/backup/run', [SystemBackupController::class, 'run'])->name('backup.run');
        Route::get('/backup/download/{file}', [SystemBackupController::class, 'download'])->name('backup.download');
        Route::post('/backup/restore', [SystemBackupController::class, 'restore'])->name('backup.restore');
    });

    Route::group(['prefix' => 'teacher', 'as' => 'teacher.'], function() {
        Route::get('/manage', [TeacherController::class, 'manage'])->name('manage');
        Route::get('/data', [TeacherController::class, 'getData'])->name('data');
        Route::get('/create', [TeacherController::class, 'create'])->name('create');
        Route::post('/store', [TeacherController::class, 'store'])->name('store');
        Route::get('/edit/{id}', [TeacherController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [TeacherController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [TeacherController::class, 'destroy'])->name('delete');
    });


    // Fingerprint
    Route::group(['prefix' => 'fingerprint', 'as' => 'fingerprint.'], function() {
        Route::get('/', [FingerprintController::class, 'index'])->name('register');
        Route::post('/log', [FingerprintController::class,'scan']);
    });

    // Simulator
    Route::group(['prefix' => 'simulator', 'as' => 'simulator.'], function() {
        Route::get('/fingerprint', [DeviceFingerprintController::class, 'index'])->name('fingerprint.index');
        Route::get('/fingerprint/class/{id}', [DeviceFingerprintController::class, 'showClass'])->name('class.show');
        Route::post('/fingerprint/register', [DeviceFingerprintController::class, 'register'])->name('fingerprint.register');
        Route::get('/fingerprint/scan', [DeviceFingerprintController::class, 'scan'])->name('fingerprint.scan');
        Route::delete('/fingerprint/delete{id}', [DeviceFingerprintController::class, 'clear'])->name('fingerprint.clear');

    });

    // Device
    Route::group(['prefix' => 'device', 'as' => 'device.'], function() {
        Route::post('/fingerprint/mode',[FingerprintModeController::class, 'set'])->name('fingerprint.mode');
        Route::get('/fingerprints/attendance', [FingerprintController::class, 'attedance'])->name('fingerprints.attendance');
        Route::get('/fingerprints/class', [FingerprintController::class, 'student_reset'])->name('fingerprints.student_reset');
        Route::get('/fingerprints/class/{id}', [FingerprintController::class, 'showClass'])->name('fingerprints.class.show');
        Route::post('/students/{student}/reset-fingerprint', [FingerprintController::class, 'reset'])->name('students.resetFingerprint');
        Route::get('/fingerprints/pending',[PendingFingerprintController::class, 'index'])->name('fingerprints.pending');
        Route::post('/fingerprints/map',[PendingFingerprintController::class, 'map'])->name('fingerprints.map');

    });


});



    // Route::post('/fingerprint/log', [FingerprintController::class,'scan']);
    // Route::post('/fingerprint/{student}', [FingerprintController::class,'update']);




require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\ScormController;
use App\Http\Controllers\ScormFileController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/scorm', [ScormController::class, 'index'])->name('scorm.index');
    Route::get('/scorm/{scorm}', [ScormController::class, 'show'])->name('scorm.show');

    Route::get('/scorm/{scorm}/file/{path}', [ScormFileController::class, 'show'])
        ->where('path', '.*')
        ->name('scorm.file');
});

<?php

use App\Http\Controllers\ScormController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/scorm', [ScormController::class, 'index'])->name('scorm.index');
    Route::get('/scorm/{scorm}', [ScormController::class, 'show'])->name('scorm.show');
    Route::post('/scorm', [ScormController::class, 'store'])->name('scorm.store');
});

<?php

use Illuminate\Support\Facades\Route;
use Modules\Media\Http\Controllers\MediaController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function (){
	Route::post('media', [MediaController::class, 'store'])->name('media.store');
});

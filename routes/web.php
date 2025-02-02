<?php

use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ImageController::class, 'index'])->name('upload.form');
Route::post('/remove-background', [ImageController::class, 'removeBackground'])->name('remove.background');

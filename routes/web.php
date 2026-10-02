<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingPageController;

Route::get('/', LandingPageController::class)->name('landing');
Route::get('/admin/preview', \App\Http\Controllers\PreviewPageController::class)->name('admin.preview');

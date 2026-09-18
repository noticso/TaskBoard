<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;

Route::inertia('/', 'Welcome')->name('home');
Route::middleware('auth')->group(function () {
    Route::apiResource('projects', ProjectController::class); // usiamo apiResource così non utilizziamo create ed edit del CRUD
});
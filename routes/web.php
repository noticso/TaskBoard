<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');
Route::middleware('auth')->group(function () {
    Route::apiResource('projects', ProjectController::class); // usiamo apiResource così non utilizziamo create ed edit del CRUD
    Route::apiResource('projects.tasks', TaskController::class)->scoped();
    Route::patch('projects/{project}/tasks/{task}/complete', [TaskController::class, 'complete'])
        ->name('projects.tasks.complete')
        ->scopeBindings();
});

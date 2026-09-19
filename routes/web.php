<?php

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectPageController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskPageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    return $request->user()
        ? redirect()->route('pages.projects.index')
        : redirect()->route('login');
})->name('home');

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {
    Route::apiResource('projects', ProjectController::class); // usiamo apiResource così non utilizziamo create ed edit del CRUD
    Route::apiResource('projects.tasks', TaskController::class)->scoped();
    Route::patch('projects/{project}/tasks/{task}/complete', [TaskController::class, 'complete'])
        ->name('projects.tasks.complete')
        ->scopeBindings();
});

// Pagine Inertia (frontend), separate dalle rotte API JSON sopra.
Route::prefix('app')->name('pages.')->middleware('auth')->group(function () {
    Route::resource('projects', ProjectPageController::class);
    Route::resource('projects.tasks', TaskPageController::class)->except(['show'])->scoped();
    Route::patch('projects/{project}/tasks/{task}/complete', [TaskPageController::class, 'complete'])
        ->name('projects.tasks.complete')
        ->scopeBindings();
});

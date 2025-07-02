<?php

use App\Http\Controllers\DiskUsageModuleController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SonarrController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Dashboard');
})->name('dashboard');


Route::get('/settings', function () {
    return Inertia::render('Settings');
})->name('settings');

Route::post('settings/test', [SettingsController::class, 'test'])->name('setting.test');
Route::post('settings/save', [SettingsController::class, 'save'])->name('setting.save');



// Module routes
Route::get('/modules', [ModuleController::class, 'index'])->name('modules.index');
Route::get('/modules/instances', [ModuleController::class, 'getInstances'])->name('modules.instances');
Route::post('/modules/instances', [ModuleController::class, 'createInstance'])->name('modules.instances.create');
Route::put('/modules/instances/{id}', [ModuleController::class, 'updateInstance'])->name('modules.instances.update');
Route::delete('/modules/instances/{id}', [ModuleController::class, 'deleteInstance'])->name('modules.instances.delete');
Route::post('/modules/instances/order', [ModuleController::class, 'updateDisplayOrder'])->name('modules.instances.order');

// Disk Usage Module routes
Route::get('/modules/disk-usage/data', [DiskUsageModuleController::class, 'getDiskSpaceData'])->name('modules.disk-usage.data');
Route::post('/modules/disk-usage/create', [DiskUsageModuleController::class, 'createDiskUsageModule'])->name('modules.disk-usage.create');
Route::get('/modules/disk-usage/available-paths', [DiskUsageModuleController::class, 'getAvailableDiskPaths'])->name('modules.disk-usage.available-paths');

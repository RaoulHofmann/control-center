<?php

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


Route::get('/sonarr/disk_space', [SonarrController::class, 'getDiskSpace'])->name('sonarr.disk_space');

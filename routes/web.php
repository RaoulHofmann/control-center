<?php

use App\Http\Controllers\SettingsController;
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

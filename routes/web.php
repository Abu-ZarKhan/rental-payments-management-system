<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DueController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\BuildingController;
use App\Http\Controllers\Admin\ContractController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\DueCategoryController;
use App\Http\Controllers\Admin\NationalityController;

// added for download
use App\Http\Controllers\ContractDownloadController;

// Add this route OUTSIDE the localized group
Route::get('/contract-download/{id}', ContractDownloadController::class)
    ->name('contracts.download')
    ->middleware(['auth']);
// added for download

// Route::get('/contract/{id}/download', ContractDownloadController::class);
Route::get('/contract/{id}/view', [ContractController::class, 'viewContract'])->name('contract.view');

// routes/web.php
Route::get('/contract/download/{id}', [ContractController::class, 'DownloadContract'])->name('contract.print');
Route::get('/contracts/{id}/word', [ContractController::class, 'generateWordContract'])->name('contracts.word');
//word doc route to download
Route::get('/download-contract', [ContractController::class, 'downloadContract'])->name('contract-download');

Route::view('/', 'frontend.home')->name('home');

Route::group(['middleware' => ['auth'], 'prefix' => 'admin', 'as' => 'admin.'], function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::group(["prefix" => "tenants", "as" => "tenants."], function () {
        Route::get('/', [TenantController::class, 'index'])->name('index');
        Route::get('/{tenant}', [TenantController::class, 'show'])->name('show');
    });

    Route::group(["prefix" => "buildings", "as" => "buildings."], function () {
        Route::get('/', [BuildingController::class, 'index'])->name('index');
        Route::get('/{building}', [BuildingController::class, 'show'])->name('show');
    });

    Route::get('contracts/{contract}', [ContractController::class, 'show'])->name('contracts.show');

    Route::get('dues/{due}', [DueController::class, 'show'])->name('dues.show');

    Route::group(["prefix" => "users", "as" => "users.",], function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/{user}', [UserController::class, 'show'])->name('show');
    });

    Route::group(["prefix" => "due-categories", "as" => "due-categories.",], function () {
        Route::get('/', [DueCategoryController::class, 'index'])->name('index');
    });

    Route::group(["prefix" => "nationalities", "as" => "nationalities.",], function () {
        Route::get('/', [NationalityController::class, 'index'])->name('index');
    });

    Route::group(["prefix" => "settings", "as" => "settings.",], function () {
        Route::get('/account', [SettingController::class, 'account'])->name('account');
        Route::get('/application', [SettingController::class, 'application'])->name('application');
    });

    Route::get('activity-log', [ActivityLogController::class, 'index'])->name('activity-log');

});

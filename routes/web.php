<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\FotoKenanganController;
use App\Http\Controllers\Admin\GuruAccountController;
use App\Http\Controllers\Guru\GuruController;
// use App\Http\Controllers\Guru\SiswaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'showLogin'])->name('login');
Route::post('/', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/import-csv', [AdminController::class, 'importSiswaCsv'])->name('admin.import');
    Route::post('/upload-kenangan', [AdminController::class, 'uploadFotoKenangan'])->name('admin.upload_kenangan');
    Route::delete('/destroy-all', [AdminController::class, 'destroyAll'])->name('admin.destroy_all');
    Route::put('/siswa/{id}', [AdminController::class, 'updateSiswa'])->name('admin.siswa.update');
    Route::delete('/siswa/{id}', [AdminController::class, 'destroySiswa'])->name('admin.siswa.destroy');
    Route::prefix('foto-kenangan')->name('admin.foto_kenangan.')->group(function () {
        Route::get('/', [FotoKenanganController::class, 'index'])->name('index');
        Route::post('/upload', [FotoKenanganController::class, 'upload'])->name('upload');
        Route::put('/{id}', [FotoKenanganController::class, 'update'])->name('update');
        Route::delete('/bulk-delete', [FotoKenanganController::class, 'bulk_delete'])->name('bulk_delete');
        Route::delete('/{id}', [FotoKenanganController::class, 'destroy'])->name('delete');
    });
    Route::prefix('guru')->name('admin.guru.')->group(function () {
        Route::get('/', [GuruAccountController::class, 'index'])->name('index');
        Route::post('/', [GuruAccountController::class, 'store'])->name('store');
        Route::put('/{id}', [GuruAccountController::class, 'update'])->name('update');
        Route::delete('/{id}', [GuruAccountController::class, 'destroy'])->name('destroy');
    });
});

Route::prefix('guru')->name('guru.')->middleware(['auth', 'role:guru'])->group(function () {
    Route::get('/dashboard', [GuruController::class, 'dashboard'])->name('dashboard');
    Route::prefix('siswa')->name('siswa.')->group(function () {
        Route::get('/',           [GuruController::class, 'siswaIndex'])->name('index');
        Route::get('/create',     [GuruController::class, 'siswaCreate'])->name('create');
        Route::post('/',          [GuruController::class, 'siswaStore'])->name('store');
        Route::get('/{id}/edit',  [GuruController::class, 'siswaEdit'])->name('edit');
        Route::put('/{id}',       [GuruController::class, 'siswaUpdate'])->name('update');
        Route::delete('/{id}',    [GuruController::class, 'siswaDestroy'])->name('destroy');
    });
    Route::prefix('foto')->name('foto.')->group(function () {
        Route::get('/',           [GuruController::class, 'fotoIndex'])->name('index');
        Route::get('/create',     [GuruController::class, 'fotoCreate'])->name('create');
        Route::post('/',          [GuruController::class, 'fotoStore'])->name('store');
        Route::get('/{id}/edit',  [GuruController::class, 'fotoEdit'])->name('edit');
        Route::put('/{id}',       [GuruController::class, 'fotoUpdate'])->name('update');
        Route::delete('/{id}',    [GuruController::class, 'fotoDestroy'])->name('destroy');
    });
});

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\KostController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MahasiswaController;
use App\Http\Controllers\PengelolaController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// --- Rute Guest (Umum) ---
Route::get('/', [KostController::class, 'index'])->name('home');
Route::get('/kost/{id}', [KostController::class, 'show'])->name('kost.detail');

Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
Route::post('/register/mahasiswa', [AuthController::class, 'registerMahasiswa'])->name('register.mahasiswa');
Route::post('/register/pengelola', [AuthController::class, 'registerPengelola'])->name('register.pengelola');
Route::get('/pending', [AuthController::class, 'pending'])->name('pending');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// --- Rute Pengguna Terautentikasi (Group Auth) ---
Route::middleware(['auth'])->group(function () {

    // --- Rute Peran Mahasiswa ---
    Route::prefix('mahasiswa')->middleware('role:mahasiswa')->group(function () {
        Route::get('/', [MahasiswaController::class, 'dashboard'])->name('mahasiswa.dashboard');
        
        Route::get('/profil', [MahasiswaController::class, 'profil'])->name('mahasiswa.profil');
        Route::post('/profil', [MahasiswaController::class, 'updateProfil'])->name('mahasiswa.profil.update');
        
        Route::get('/rekomendasi', [MahasiswaController::class, 'rekomendasi'])->name('mahasiswa.rekomendasi');
        Route::post('/rekomendasi/save', [MahasiswaController::class, 'savePreferensi'])->name('mahasiswa.preferensi.save');
        Route::post('/rekomendasi/hapus', [MahasiswaController::class, 'clearPreferensi'])->name('mahasiswa.preferensi.clear');
        
        Route::get('/favorit', [MahasiswaController::class, 'favorit'])->name('mahasiswa.favorit');
        Route::post('/favorit/{id}/toggle', [MahasiswaController::class, 'toggleFavorit'])->name('mahasiswa.favorit.toggle');
        
        Route::get('/kost/{id}/kontak/{type}', [MahasiswaController::class, 'contact'])->name('mahasiswa.contact');
    });

    // --- Rute Peran Pengelola Kost ---
    Route::prefix('pengelola')->middleware('role:pengelola')->group(function () {
        Route::get('/', [PengelolaController::class, 'dashboard'])->name('pengelola.dashboard');
        
        Route::get('/profil', [PengelolaController::class, 'profil'])->name('pengelola.profil');
        Route::post('/profil/update', [PengelolaController::class, 'updateProfil'])->name('pengelola.profil.update');
        
        Route::get('/kost', [PengelolaController::class, 'kost'])->name('pengelola.kost');
        Route::get('/kost/tambah', [PengelolaController::class, 'tambahKost'])->name('pengelola.kost.tambah');
        Route::post('/kost/simpan', [PengelolaController::class, 'simpanKost'])->name('pengelola.kost.simpan');
        Route::get('/kost/{id}/edit', [PengelolaController::class, 'editKost'])->name('pengelola.kost.edit');
        Route::post('/kost/{id}/update', [PengelolaController::class, 'updateKost'])->name('pengelola.kost.update');
        Route::post('/kost/{id}/hapus', [PengelolaController::class, 'hapusKost'])->name('pengelola.kost.hapus');
        
        Route::get('/kost/{id}/fasilitas', [PengelolaController::class, 'fasilitas'])->name('pengelola.kost.fasilitas');
        Route::post('/kost/{id}/fasilitas/simpan', [PengelolaController::class, 'simpanFasilitas'])->name('pengelola.kost.fasilitas.simpan');
        
        Route::get('/kost/{id}/fotos', [PengelolaController::class, 'fotos'])->name('pengelola.kost.fotos');
        Route::post('/kost/{id}/fotos/simpan', [PengelolaController::class, 'simpanFoto'])->name('pengelola.kost.fotos.simpan');
        Route::post('/kost/{id}/fotos/{foto_id}/hapus', [PengelolaController::class, 'hapusFoto'])->name('pengelola.kost.fotos.hapus');
        Route::post('/kost/{id}/fotos/{foto_id}/utama', [PengelolaController::class, 'setFotoUtama'])->name('pengelola.kost.fotos.utama');

        // CRUD Kamar Kost
        Route::get('/kost/{kost_id}/kamar', [PengelolaController::class, 'kamar'])->name('pengelola.kost.kamar');
        Route::get('/kost/{kost_id}/kamar/tambah', [PengelolaController::class, 'tambahKamar'])->name('pengelola.kost.kamar.tambah');
        Route::post('/kost/{kost_id}/kamar/simpan', [PengelolaController::class, 'simpanKamar'])->name('pengelola.kost.kamar.simpan');
        Route::get('/kost/{kost_id}/kamar/{id}/edit', [PengelolaController::class, 'editKamar'])->name('pengelola.kost.kamar.edit');
        Route::post('/kost/{kost_id}/kamar/{id}/update', [PengelolaController::class, 'updateKamar'])->name('pengelola.kost.kamar.update');
        Route::post('/kost/{kost_id}/kamar/{id}/hapus', [PengelolaController::class, 'hapusKamar'])->name('pengelola.kost.kamar.hapus');
    });

    // --- Rute Peran Admin ---
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        
        Route::get('/pengguna', [AdminController::class, 'pengguna'])->name('admin.pengguna');
        Route::post('/pengguna/{id}/toggle', [AdminController::class, 'toggleStatus'])->name('admin.pengguna.toggle');
        
        Route::get('/pengajuan', [AdminController::class, 'pengajuan'])->name('admin.pengajuan');
        Route::post('/pengajuan/{id}/verifikasi', [AdminController::class, 'verifikasiPengelola'])->name('admin.pengajuan.verifikasi');
        
        Route::get('/kriteria', [AdminController::class, 'kriteria'])->name('admin.kriteria');
        Route::post('/kriteria/simpan', [AdminController::class, 'simpanKriteria'])->name('admin.kriteria.simpan');
        Route::post('/kriteria/{id}/update', [AdminController::class, 'updateKriteria'])->name('admin.kriteria.update');
        Route::post('/kriteria/{id}/hapus', [AdminController::class, 'hapusKriteria'])->name('admin.kriteria.hapus');
        
        Route::post('/kriteria/{id}/opsi', [AdminController::class, 'simpanOpsi'])->name('admin.kriteria.opsi.simpan');
        Route::post('/kriteria/opsi/{opsi_id}/hapus', [AdminController::class, 'hapusOpsi'])->name('admin.kriteria.opsi.hapus');
        
        Route::get('/log', [AdminController::class, 'logs'])->name('admin.logs');
    });

});

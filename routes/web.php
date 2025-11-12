<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PpiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuratJalanController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\MasterBarangController;


Route::get('/', function () {
    return view('welcome');
});

//login
Route::get ('login',[AuthController::class, 'login'])-> name('login');
Route::post ('login',[AuthController::class, 'loginProses'])-> name('loginProses');

//logout
Route::get ('logout',[AuthController::class, 'logout'])-> name('logout');


//route group login function 
Route::middleware('checkLogin')->group(function(){
  //dashboard
Route::get ('dashboard',[DashboardController::class, 'index'])->name('dashboard');

//ppi
Route::get ('ppi',[PpiController::class, 'index'])-> name('ppi');  

//team
Route::get ('team',[TeamController::class, 'index'])-> name('team');

//CRUD Team

//Create
Route::get('/team/create', [TeamController::class, 'create'])->name('team.create');
Route::post('/team/store', [TeamController::class, 'store'])->name('team.store');

//Edit
Route::get('/team/edit/{id}', [TeamController::class, 'edit'])->name('team.edit');
Route::put('/team/update/{id}', [TeamController::class, 'update'])->name('team.update');

//Delete
Route::delete('/team/destroy/{id}', [TeamController::class, 'destroy'])->name('team.destroy');

//surat jalan
Route::resource('surat-jalan', SuratJalanController::class);

//Pengguna
Route::resource('pengguna', PenggunaController::class);

//barang masuk
  Route::resource('barangmasuk', BarangMasukController::class);

//master barang
Route::resource('master-barang', MasterBarangController::class);

// --- ROUTE BUAT BARANG KELUAR (BAST) ---
Route::prefix('barangkeluar')->name('barangkeluar.')->group(function () {
    
    // 1. Tampilkan Form BAST
    // URL: /barang-keluar/create
    Route::get('/create', [BarangKeluarController::class, 'create'])->name('create');
    
    // 2. Proses Simpan Form BAST
    // URL: /barang-keluar/store (Method: POST)
    Route::post('/store', [BarangKeluarController::class, 'store'])->name('store');
    
    // 3. FUNGSI AJAX (Buat "Otomatis Ke Isi")
    // URL: /barang-keluar/get-asset-details
    Route::get('/get-asset-details', [BarangKeluarController::class, 'getAssetDetails'])->name('getAssetDetails');
    Route::get('/', [BarangKeluarController::class, 'index'])->name('index');
    
    // 5. Halaman "Detail 1 BAST" (Buat liat TTD & Foto)
    Route::get('/show/{id}', [BarangKeluarController::class, 'show'])->name('show');

});

});
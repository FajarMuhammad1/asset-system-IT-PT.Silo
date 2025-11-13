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
use App\Http\Controllers\PenggunaDashboardController;
use App\Http\Controllers\StaffDashboardController;


Route::get('/', function () {
    return view('welcome');
});

//login
Route::get ('login',[AuthController::class, 'login'])-> name('login');
Route::post ('login',[AuthController::class, 'loginProses'])-> name('loginProses');

//logout
Route::get ('logout',[AuthController::class, 'logout'])-> name('logout');


// --- KAVLING 1: SUPER ADMIN & ADMIN ---
// Cuma 'super admin' dan 'admin' yang boleh masuk
Route::middleware(['checkLogin:SuperAdmin,Admin'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // --- SEMUA RUTE ADMIN LO MASUK SINI ---
    Route::get ('ppi',[PpiController::class, 'index'])-> name('ppi'); 
    
    //Team
    Route::get ('team',[TeamController::class, 'index'])-> name('team');
    Route::get('/team/create', [TeamController::class, 'create'])->name('team.create');
    Route::post('/team/store', [TeamController::class, 'store'])->name('team.store');
    Route::get('/team/edit/{id}', [TeamController::class, 'edit'])->name('team.edit');
    Route::put('/team/update/{id}', [TeamController::class, 'update'])->name('team.update');
    Route::delete('/team/destroy/{id}', [TeamController::class, 'destroy'])->name('team.destroy');
    
    //Surat Jalan
    Route::resource('surat-jalan', SuratJalanController::class);
    
    //Pengguna (CRUD User)
    Route::resource('pengguna', PenggunaController::class);
    
    //Barang Masuk (Aset)
    Route::resource('barangmasuk', BarangMasukController::class);
    
    //Master Barang (Katalog)
    Route::resource('master-barang', MasterBarangController::class);
    
    //Barang Keluar (BAST)
    Route::prefix('barangkeluar')->name('barangkeluar.')->group(function () {
        Route::get('/create', [BarangKeluarController::class, 'create'])->name('create');
        Route::post('/store', [BarangKeluarController::class, 'store'])->name('store');
        Route::get('/get-asset-details', [BarangKeluarController::class, 'getAssetDetails'])->name('getAssetDetails');
        Route::get('/', [BarangKeluarController::class, 'index'])->name('index');
        Route::get('/show/{id}', [BarangKeluarController::class, 'show'])->name('show');
    });

});


// --- KAVLING 2: PENGGUNA ---
Route::middleware(['checkLogin:Pengguna'])->prefix('Pengguna')->name('pengguna.')->group(function () {
    
    Route::get('/dashboard', [PenggunaDashboardController::class, 'index'])->name('dashboard');
    // Nanti rute 'permintaan.create' (PPI) lo taruh sini

});


// --- KAVLING 3: STAFF ---
Route::middleware(['checkLogin:Staff'])->prefix('Staff')->name('staff.')->group(function () {
    
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    // Nanti rute 'helpdesk.index' (Tugas Staff) lo taruh sini

});
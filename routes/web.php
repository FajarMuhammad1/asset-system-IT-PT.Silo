<?php

use Illuminate\Support\Facades\Route;

// KONTROLER UTAMA
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PenggunaController;

// KONTROLER ADMIN
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuratJalanController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\BarangKeluarController;
use App\Http\Controllers\MasterBarangController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\HelpdeskController;
use App\Http\Controllers\Admin\TaskReportAdminController;
use App\Http\Controllers\Admin\PpiAdminController;

// KONTROLER STAFF
use App\Http\Controllers\Staff\StaffHelpdeskController;
use App\Http\Controllers\Staff\StaffReportController;

// KONTROLER PENGGUNA
use App\Http\Controllers\PenggunaDashboardController;
use App\Http\Controllers\Pengguna\PpiController as PenggunaPpiController;
use App\Http\Controllers\Pengguna\TicketController as PenggunaTicketController;
use App\Http\Controllers\UserBASTController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// AUTHENTICATION
Route::get('login', [AuthController::class, 'login'])->name('login');
Route::post('login', [AuthController::class, 'loginProses'])->name('loginProses');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

// PROFILE & PASSWORD (ALL AUTH USER)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});


// ====================================================
// KAVLING 1: SUPER ADMIN & ADMIN
// ====================================================
Route::middleware(['checkLogin:SuperAdmin,Admin'])->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    // --- GROUP KHUSUS URL "/admin/..." ---
    Route::prefix('admin')->name('admin.')->group(function() {

        // 1. MONITORING PPI
        Route::get('/ppi-monitoring/export', [PpiAdminController::class, 'exportExcel'])->name('ppi.export'); 
        Route::get('/ppi-monitoring', [PpiAdminController::class, 'index'])->name('ppi.index');
        Route::get('/ppi-monitoring/{id}', [PpiAdminController::class, 'show'])->name('ppi.show');
        Route::put('/ppi-monitoring/{id}/update', [PpiAdminController::class, 'updateStatus'])->name('ppi.update');

        // 2. HELPDESK ADMIN
        Route::get('/helpdesk', [HelpdeskController::class, 'index'])->name('helpdesk.index');
        Route::get('/helpdesk/{id}', [HelpdeskController::class, 'show'])->name('helpdesk.show');
        Route::post('/helpdesk/{id}/assign', [HelpdeskController::class, 'assignTeknisi'])->name('helpdesk.assign');
    });

    // --- MASTER DATA & RESOURCE ---
    
    // Team
    Route::get('team', [TeamController::class, 'index'])->name('team');
    Route::get('/team/create', [TeamController::class, 'create'])->name('team.create');
    Route::post('/team/store', [TeamController::class, 'store'])->name('team.store');
    Route::get('/team/edit/{id}', [TeamController::class, 'edit'])->name('team.edit');
    Route::put('/team/update/{id}', [TeamController::class, 'update'])->name('team.update');
    Route::delete('/team/destroy/{id}', [TeamController::class, 'destroy'])->name('team.destroy');
    
    // Resource Controllers
    Route::resource('surat-jalan', SuratJalanController::class);
    Route::resource('pengguna', PenggunaController::class);
    Route::resource('master-barang', MasterBarangController::class);

    // Barang Masuk (Aset)
    Route::resource('barangmasuk', BarangMasukController::class);
    Route::get('/barangmasuk/{id}/cetak-label', [BarangMasukController::class, 'cetakLabel'])->name('barangmasuk.cetak_label');
    
    // Fitur Scan Barcode Barang Masuk
    Route::get('/scan', [BarangMasukController::class, 'scanPage'])->name('scan.index');
    Route::post('/scan/cek', [BarangMasukController::class, 'processScan'])->name('scan.process');

    // Barang Keluar (BAST)
    Route::prefix('barangkeluar')->name('barangkeluar.')->group(function () {
        Route::get('/', [BarangKeluarController::class, 'index'])->name('index');
        Route::get('/create', [BarangKeluarController::class, 'create'])->name('create');
        Route::get('/get-asset-details', [BarangKeluarController::class, 'getAssetDetails'])->name('getAssetDetails');
        Route::post('/store', [BarangKeluarController::class, 'store'])->name('store');
        Route::get('/{id}', [BarangKeluarController::class, 'show'])->name('show');
        Route::post('/{id}/admin-sign', [BarangKeluarController::class, 'adminSign'])->name('adminSign');
    });

    // Activity Log
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity.log');

    // --- TASK REPORT ADMIN (Updated) ---
    // Route Export WAJIB DI ATAS Route {id} / Show
    Route::get('/task-reports/export', [TaskReportAdminController::class, 'exportExcel'])->name('admin.task_report.export');
    Route::get('/task-reports', [TaskReportAdminController::class, 'index'])->name('taskreport.index');
    Route::get('/task-reports/{id}', [TaskReportAdminController::class, 'show'])->name('taskreport.show');

});


// ====================================================
// KAVLING 2: PENGGUNA
// ====================================================
Route::middleware(['checkLogin:Pengguna'])->prefix('Pengguna')->name('pengguna.')->group(function () {
    
    Route::get('/dashboard', [PenggunaDashboardController::class, 'index'])->name('dashboard');
    
    // PPI Pengguna
    Route::get('/ppi/create', [PenggunaPpiController::class, 'create'])->name('ppi.create');
    Route::post('/ppi/store', [PenggunaPpiController::class, 'store'])->name('ppi.store');
    Route::get('/ppi/riwayat', [PenggunaPpiController::class, 'index'])->name('ppi.index'); 
    
    // Helpdesk (Tiket)
    Route::get('/helpdesk', [PenggunaTicketController::class, 'index'])->name('helpdesk.index');
    Route::get('/helpdesk/create', [PenggunaTicketController::class, 'create'])->name('helpdesk.create');
    Route::post('/helpdesk', [PenggunaTicketController::class, 'store'])->name('helpdesk.store');
    Route::get('/helpdesk/{id}', [PenggunaTicketController::class, 'show'])->name('helpdesk.show');

    // BAST Untuk Pengguna (TTD)
    Route::prefix('user/bast')->name('userbast.')->group(function () {
        Route::get('/', [UserBASTController::class, 'index'])->name('index');
        Route::get('/sign/{id}', [UserBASTController::class, 'sign'])->name('sign');
        Route::post('/sign/{id}', [UserBASTController::class, 'submitSign'])->name('submit');
    });

});


// ====================================================
// KAVLING 3: STAFF
// ====================================================
Route::middleware(['checkLogin:Staff'])->prefix('Staff')->name('staff.')->group(function () {
    
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    
    // Fitur Helpdesk Staff (Kerja)
    Route::get('/helpdesk', [StaffHelpdeskController::class, 'index'])->name('helpdesk.index');
    Route::get('/helpdesk/{id}', [StaffHelpdeskController::class, 'show'])->name('helpdesk.show');
    Route::post('/helpdesk/{id}/start', [StaffHelpdeskController::class, 'start'])->name('helpdesk.start');
    Route::post('/helpdesk/{id}/finish', [StaffHelpdeskController::class, 'finish'])->name('helpdesk.finish');
    Route::post('/helpdesk/{id}/reject', [StaffHelpdeskController::class, 'reject'])->name('helpdesk.reject');

    // Fitur Laporan Tugas Staff
    Route::get('/reports', [StaffReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/create', [StaffReportController::class, 'create'])->name('reports.create');
    Route::post('/reports', [StaffReportController::class, 'store'])->name('reports.store');
    Route::get('/reports/{id}', [StaffReportController::class, 'show'])->name('reports.show');

});
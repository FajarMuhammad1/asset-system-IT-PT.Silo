<?php

use Illuminate\Support\Facades\Route;

// KONTROLER UTAMA
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\DisposalController;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\RkabController;
use App\Http\Controllers\AssetLifecycleController;

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
use App\Http\Controllers\Admin\MaintenanceController; 

// KONTROLER SUPER ADMIN
use App\Http\Controllers\SuperAdminPpiController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;

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
// KAVLING 1: SHARED (SUPER ADMIN & ADMIN BISA AKSES)
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
        Route::put('/ppi-monitoring/{id}/forward', [PpiAdminController::class, 'forwardToSuperAdmin'])->name('ppi.forward');

        // 2. HELPDESK
        Route::get('/helpdesk', [HelpdeskController::class, 'index'])->name('helpdesk.index');
        Route::get('/helpdesk/{id}', [HelpdeskController::class, 'show'])->name('helpdesk.show');
        Route::post('/helpdesk/{id}/assign', [HelpdeskController::class, 'assignTeknisi'])->name('helpdesk.assign');
        Route::put('/helpdesk/{id}/settings', [HelpdeskController::class, 'updateSettings'])->name('helpdesk.settings');

        // 3. MAINTENANCE ASET RUTIN
        Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
        Route::post('/maintenance/schedule', [MaintenanceController::class, 'storeSchedule'])->name('maintenance.schedule.store');
        Route::put('/maintenance/tugas/{id}/selesai', [MaintenanceController::class, 'selesaikanPerawatan'])->name('maintenance.tugas.selesai');
    });

    // --- MASTER DATA (REFERENSI) ---
    Route::get('team', [TeamController::class, 'index'])->name('team');
    Route::get('/team/create', [TeamController::class, 'create'])->name('team.create');
    Route::post('/team/store', [TeamController::class, 'store'])->name('team.store');
    Route::get('/team/edit/{id}', [TeamController::class, 'edit'])->name('team.edit');
    Route::put('/team/update/{id}', [TeamController::class, 'update'])->name('team.update');
    Route::delete('/team/destroy/{id}', [TeamController::class, 'destroy'])->name('team.destroy');
    
    Route::resource('pengguna', PenggunaController::class);
    Route::resource('master-barang', MasterBarangController::class);

    // Activity Log & Task Report
    Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity.log');
    Route::get('/task-reports/export', [TaskReportAdminController::class, 'exportExcel'])->name('admin.task_report.export');
    Route::get('/task-reports', [TaskReportAdminController::class, 'index'])->name('taskreport.index');
    Route::get('/task-reports/{id}', [TaskReportAdminController::class, 'show'])->name('taskreport.show');

    // --- MODUL DISPOSAL, MUTASI, RKAB & ASSET LIFECYCLE ---
    Route::get('/disposal', [DisposalController::class, 'index'])->name('disposal.index');
    Route::get('/disposal/{id}/print', [DisposalController::class, 'print'])->name('disposal.print');
    
    Route::get('/mutasi', [MutasiController::class, 'index'])->name('mutasi.index');

    Route::get('/rkab-analysis', [RkabController::class, 'index'])->name('rkab.index');
    Route::post('/rkab-analysis', [RkabController::class, 'store'])->name('rkab.store');
    Route::put('/rkab-analysis/{id}', [RkabController::class, 'update'])->name('rkab.update');
    Route::delete('/rkab-analysis/{id}', [RkabController::class, 'destroy'])->name('rkab.destroy');
    Route::get('/rkab-analysis/print', [RkabController::class, 'print'])->name('rkab.print');

    // --- LOG LIFECYCLE ---
    Route::get('/asset-lifecycle', [AssetLifecycleController::class, 'index'])->name('asset-lifecycle.index');
    Route::get('/asset-lifecycle/track', [AssetLifecycleController::class, 'track'])->name('asset-lifecycle.track');
    
    // UPDATE: Mengubah nama rute menjadi 'asset.cetak_lifecycle' agar sinkron dengan file Blade tracking sebelumnya
    Route::get('/asset-lifecycle/{id}/cetak', [AssetLifecycleController::class, 'cetakLifecycle'])->name('asset.cetak_lifecycle');
});


// ====================================================
// KAVLING 2: OPERASIONAL KHUSUS ADMIN
// ====================================================
Route::middleware(['checkLogin:Admin'])->group(function () {

    // --- [SURAT JALAN & BARANG MASUK] ---
    Route::get('surat-jalan/export/excel', [SuratJalanController::class, 'exportExcelFiltered'])->name('surat-jalan.export-excel');
    Route::get('surat-jalan/export/pdf', [SuratJalanController::class, 'exportPdfFiltered'])->name('surat-jalan.export-pdf');
    Route::get('surat-jalan/{id}/cetak', [SuratJalanController::class, 'exportPdf'])->name('surat-jalan.cetak-pdf');
    Route::resource('surat-jalan', SuratJalanController::class);

    // BARANG MASUK
    Route::get('/barangmasuk/export', [BarangMasukController::class, 'exportExcel'])->name('barangmasuk.export');
    Route::resource('barangmasuk', BarangMasukController::class);
    Route::get('/barangmasuk/{id}/cetak-label', [BarangMasukController::class, 'cetakLabel'])->name('barangmasuk.cetak_label');
    
    Route::get('/scan', [BarangMasukController::class, 'scanPage'])->name('scan.index');
    Route::post('/scan/cek', [BarangMasukController::class, 'processScan'])->name('scan.process');

    // --- BARANG KELUAR (BAST) ---
    Route::prefix('barangkeluar')->name('barangkeluar.')->group(function () {
        Route::get('/', [BarangKeluarController::class, 'index'])->name('index');
        Route::get('/create', [BarangKeluarController::class, 'create'])->name('create');
        Route::get('/get-asset-details', [BarangKeluarController::class, 'getAssetDetails'])->name('getAssetDetails');
        Route::post('/store', [BarangKeluarController::class, 'store'])->name('store');
        Route::get('/{id}/cetak', [BarangKeluarController::class, 'cetakBast'])->name('cetak');
        Route::get('/{id}', [BarangKeluarController::class, 'show'])->name('show');
        Route::post('/{id}/admin-sign', [BarangKeluarController::class, 'adminSign'])->name('adminSign');
    });

    Route::post('/disposal', [DisposalController::class, 'store'])->name('disposal.store');
    Route::post('/mutasi', [MutasiController::class, 'store'])->name('mutasi.store');
});


// ====================================================
// KAVLING 3: KHUSUS SUPER ADMIN (APPROVAL)
// ====================================================
Route::middleware(['checkLogin:SuperAdmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/approval', [SuperAdminPpiController::class, 'index'])->name('approval.index');
    Route::get('/approval/{id}/review', [SuperAdminPpiController::class, 'showReview'])->name('approval.review');
    Route::put('/approval/{id}/approve', [SuperAdminPpiController::class, 'approve'])->name('approval.approve');
    Route::put('/approval/{id}/reject', [SuperAdminPpiController::class, 'reject'])->name('approval.reject');

    Route::post('/disposal/{id}/approve', [DisposalController::class, 'approve'])->name('disposal.approve');
    Route::post('/disposal/{id}/reject', [DisposalController::class, 'reject'])->name('disposal.reject');
});


// ====================================================
// KAVLING 4: PENGGUNA
// ====================================================
Route::middleware(['checkLogin:Pengguna'])->prefix('Pengguna')->name('pengguna.')->group(function () {
    Route::get('/dashboard', [PenggunaDashboardController::class, 'index'])->name('dashboard');
    
    Route::get('/ppi/create', [PenggunaPpiController::class, 'create'])->name('ppi.create');
    Route::post('/ppi/store', [PenggunaPpiController::class, 'store'])->name('ppi.store');
    Route::get('/ppi/export/pdf', [PenggunaPpiController::class, 'exportPdf'])->name('pengguna.ppi.pdf'); 
    Route::get('/ppi/riwayat', [PenggunaPpiController::class, 'index'])->name('ppi.index'); 
    
    Route::get('/helpdesk', [PenggunaTicketController::class, 'index'])->name('helpdesk.index');
    Route::get('/helpdesk/create', [PenggunaTicketController::class, 'create'])->name('helpdesk.create');
    Route::post('/helpdesk', [PenggunaTicketController::class, 'store'])->name('helpdesk.store');
    Route::get('/helpdesk/{id}', [PenggunaTicketController::class, 'show'])->name('helpdesk.show');

    Route::prefix('user/bast')->name('userbast.')->group(function () {
        Route::get('/', [UserBASTController::class, 'index'])->name('index');
        Route::get('/sign/{id}', [UserBASTController::class, 'sign'])->name('sign');
        Route::post('/sign/{id}', [UserBASTController::class, 'submitSign'])->name('submit');
    });
});


// ====================================================
// KAVLING 5: STAFF / TEKNISI
// ====================================================
Route::middleware(['checkLogin:Staff'])->prefix('Staff')->name('staff.')->group(function () {
    
    Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    
    // Fitur Helpdesk Staff
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

    // === MAINTENANCE KHUSUS STAFF ===
    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::put('/maintenance/tugas/{id}/mulai', [MaintenanceController::class, 'mulaiPerawatan'])->name('maintenance.tugas.mulai');
    Route::put('/maintenance/tugas/{id}/selesai', [MaintenanceController::class, 'selesaikanPerawatan'])->name('maintenance.tugas.selesai');
});
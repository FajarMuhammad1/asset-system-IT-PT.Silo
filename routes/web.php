<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PpiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SuratJalanController;


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

});
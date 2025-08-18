<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\JornadasController;
use App\Http\Controllers\PrdController;
use App\Http\Controllers\SolicitudController;
use App\Http\Controllers\SolicitudesRelacionController;
use App\Livewire\Solicitud\Indicadores;
use App\Livewire\Solicitud\RelacionHoras;
use App\Livewire\Solicitud\RelacionJornadas;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [HomeController::class, 'index'])->name('index');
Route::middleware(['authcustom'])->get('/logout', [HomeController::class, 'logout'])->name('logout');
Route::middleware(['authcustom'])->get('/dashboard', [HomeController::class, 'main'])->name('main');

Route::get('/litoapps', function () {
    return redirect('https://servicios.litoprocess.com/litoapps/index.php');
})->name('litoapps');

Livewire::setUpdateRoute(function ($handle) {
    return Route::post('/cierreturno/livewire/update', $handle);
});

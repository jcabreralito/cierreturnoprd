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
Route::middleware(['authcustom'])->get('/relacion', RelacionHoras::class)->name('relacion');
Route::middleware(['authcustom'])->get('/relacion-jornadas', RelacionJornadas::class)->name('relacion-jornadas');
Route::middleware(['authcustom'])->get('/indicadores', Indicadores::class)->name('indicators');

Route::get('/litoapps', function () {
    return redirect('https://servicios.litoprocess.com/litoapps/index.php');
})->name('litoapps');

Livewire::setUpdateRoute(function ($handle) {
    return Route::post('/cierreturno/livewire/update', $handle);
});

/**
 * Rutas para el módulo de horas extra
 *
 */
Route::middleware(['authcustom'])->get('/getdata/{id}', [SolicitudController::class, 'showPersonal'])->name('solicitud.showPersonal');
Route::middleware(['authcustom'])->post('/getdataps', [SolicitudController::class, 'showSolicitudesPersonal'])->name('solicitud.showPersonalps');
Route::middleware(['authcustom'])->get('/getops/{id}', [SolicitudController::class, 'getOps'])->name('solicitud.getops');
Route::middleware(['authcustom'])->get('/getlastcomment/{horas_finales_id}', [SolicitudesRelacionController::class, 'getlastcomment'])->name('solicitud.getlastcomment');

Route::middleware(['authcustom'])->post('/generateReportHrs', [JornadasController::class, 'generateReportHrs'])->name('solicitud.getlastcomment');

/**
 * Ruta de prod para acciones de una sola vez
 *
 */
// Route::middleware(['authcustom'])->get('/prod/storeInicial/{sem}', [PrdController::class, 'insertData']);
// Route::middleware(['authcustom'])->get('/prod/storeConfig', [PrdController::class, 'guardarGrupos']);

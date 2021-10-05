<?php

use Freshwork\ChileanBundle\Facades\Rut;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\PropertiesController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/usuario',[UserController::class,'index'])->name('admin.user.index');
Route::post('/usuario',[UserController::class,'store'])->name('admin.user.store');
Route::get('/usuario/create',[UserController::class,'create'])->name('admin.user.create');
Route::get('/usuario/{user:rut}/edit',[UserController::class,'edit'])->name('admin.user.edit');
Route::put('/usuario/{user:rut}',[UserController::class,'update'])->name('admin.user.update');
Route::delete('/usuario/{user:rut}',[UserController::class,'destroy'])->name('admin.user.destroy');
Route::post('usuario/{user:rut}/permission',[UserController::class,'permissionShift'])->name('admin.user.permission');

Route::get('reunion/',[AdminAppointmentController::class,'index'])->name('admin.appointment.index');
Route::post('reunion/',[AdminAppointmentController::class,'store']);
Route::get('reunion/events',[AdminAppointmentController::class,'events'])->name('admin.appointment.events');
Route::get('reunion/events/archived',[AdminAppointmentController::class,'eventsArchived'])->name('admin.appointment.eventsArchived');
Route::get('reunion/activities/{appointment:slug}',[AdminAppointmentController::class,'show'])->name('admin.appointment.activity.show');
Route::post('reunion/createlink',[AdminAppointmentController::class,'createLink'])->name('admin.appointment.createlink');
Route::post('reunion/ajax',[AdminAppointmentController::class,'ajax'])->name('admin.appointment.ajax');
Route::get('reunion/{appointment:slug}',[AdminAppointmentController::class,'show'])->name('admin.appointment.show');

Route::get('/usuarios/list', [PropertiesController::class,'users'])->name('admin.users.list');
Route::get('/usuario/properties/me', [PropertiesController::class,'me'])->name('admin.users.me');
Route::get('/usuario/properties/permissions', [PropertiesController::class,'myPermissions'])->name('admin.users.me.permissions');
Route::get('/usuario/properties/permissions/{sufix}', [PropertiesController::class,'myPermissions'])->name('admin.users.me.permissions.withsufix');



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

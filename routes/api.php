<?php

use App\Http\Controllers\DoctorController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', LoginController::class);
Route::post('/logout', LogoutController::class)->middleware('auth:sanctum');

// doctors routes
Route::post('/doctors/appointments/{appointmentRequest}', [App\Http\Controllers\AppointmentController::class, 'store'])->name('appointments.store');
Route::get('/doctors/appointment-request', [App\Http\Controllers\DoctorController::class, 'getAppointmentRequests'])
    ->middleware('auth:sanctum')
    ->name('patients_appointment_requests.index');

Route::post('/doctor', App\Http\Controllers\Doctor\StoreController::class)->middleware('auth:sanctum');

// patients routes
Route::post('/patients/login', [App\Http\Controllers\PatientController::class, 'login'])->name('patients.login');
Route::post('/patients', [App\Http\Controllers\PatientController::class, 'store'])->name('patients.store');

Route::post('/patients/appointment-request', [App\Http\Controllers\PatientController::class, 'storeAppointmentRequest'])
    ->middleware('auth:sanctum')
    ->name('appointment_requests.store');

Route::get('/patients/appointment-request', [App\Http\Controllers\PatientController::class, 'getAppointmentRequests'])
    ->middleware('auth:sanctum')
    ->name('appointment_requests.index');



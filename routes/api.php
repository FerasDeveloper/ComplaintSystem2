<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('/resend-otp', [AuthController::class, 'resendOtp']);
Route::get('governments', [AuthController::class, 'getGovernments']);
Route::get('complaintTypes', [AuthController::class, 'getComplaintTypes']);

Route::middleware(['auth:sanctum'])->group(function () {
  Route::post('/logout', [AuthController::class, 'logout']);
  Route::post('/government/employee', [AuthController::class, 'createEmployee']);
  Route::post('/addgovernment', [AuthController::class, 'createGovernment']);

  // Complaint
  Route::post('addComplaint', [ComplaintController::class, 'addComplaint']);
  Route::get('showComplaint/{id}', [ComplaintController::class, 'showComplaint']);
  Route::get('getComplaints', [ComplaintController::class, 'getComplaints']);
});

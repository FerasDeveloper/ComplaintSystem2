<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateEmployeeRequest;
use App\Http\Requests\CreateGovernmentRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterCitizenRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class AuthController extends Controller
{
  protected $authService;
  public function __construct(\App\Services\AuthService $authService)
  {
    $this->authService = $authService;
  }

  public function register(RegisterCitizenRequest $req)
  {
    $data = $req->only(['name', 'email', 'phone', 'password']);
    $user = $this->authService->registerCitizen($data);
    return response()->json(['message' => 'Registered. OTP sent', 'user_id' => $user->id], 201);
  }

  public function verifyOtp(VerifyOtpRequest $req)
  {
    $user = User::find($req->user_id);
    if (!$user) {
      return response()->json(['message' => 'User not found'], 404);
    }

    if (!$this->authService->verifyOtp($user, $req->otp)) {
      return response()->json(['message' => 'Invalid or expired OTP'], 422);
    }
    $token = $user->createToken('api-token')->plainTextToken;

    return response()->json([
      'message' => 'Verified',
      'token'   => $token,
      'user'    => $user,
    ]);
  }

  public function resendOtp(Request $req)
  {
    $user = User::find($req->user_id);

    if (!$user) {
      return response()->json(['message' => 'User not found'], 404);
    }

    if ($user->is_verified) {
      return response()->json(['message' => 'Account already verified'], 400);
    }

    $this->authService->generateAndSendOtp($user);

    return response()->json(['message' => 'OTP resent']);
  }

  public function login(LoginRequest $req)
  {
    $res = $this->authService->attemptLogin($req->identifier, $req->password);
    if (!$res['success']) return response()->json(['message' => $res['message']], 401);

    $user = $res['user'];
    if (!$user->is_verified) return response()->json(['message' => 'Account not verified'], 403);

    $token = $user->createToken('api-token')->plainTextToken;
    return response()->json(['token' => $token, 'user' => $user]);
  }

  public function logout(Request $req)
  {
    $req->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'Logged out']);
  }

  public function createEmployee(CreateEmployeeRequest $req)
  {
    Gate::authorize('create-employee');
    $creator = $req->user();
    $governmentId = $creator->governments()->first()?->id;
    $data = $req->only(['name', 'email', 'phone', 'password']);
    $employee = $this->authService->createEmployeeByGovernment($data, $governmentId, $creator);
    return response()->json(['message' => 'Employee created', 'employee_id' => $employee->id], 201);
  }

  // public function createGovernment(Request $req)
  // {
  //   $creator = $req->user();

  //   if ($creator->role !== 'admin') {
  //     return response()->json(['message' => 'Only admins can create governments'], 403);
  //   }

  //   $data = $req->only(['name', 'email', 'phone', 'password', 'location', 'description']);

  //   $governmentUser = $this->authService->createGovernmentByAdmin($data, $creator);

  //   return response()->json([
  //     'message'        => 'Government user and government created',
  //     'government_user_id' => $governmentUser->id,
  //     'government_id'  => $governmentUser->governments()->first()?->id
  //   ], 201);
  // }

  public function createGovernment(CreateGovernmentRequest $req)
  {
    Gate::authorize('create-government');
    $data = $req->only(['name', 'email', 'phone', 'password', 'location', 'description']);

    $governmentUser = $this->authService->createGovernmentByAdmin($data, $req->user());

    return response()->json([
      'message'             => 'Government user and government created',
      'government_user_id'  => $governmentUser->id,
      'government_id'       => $governmentUser->governments()->first()?->id
    ], 201);
  }
}

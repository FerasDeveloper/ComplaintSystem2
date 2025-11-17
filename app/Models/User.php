<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
  /** @use HasFactory<\Database\Factories\UserFactory> */
  use HasFactory, Notifiable, HasApiTokens;

  /**
   * The attributes that are mass assignable.
   *
   * @var list<string>
   */
  // protected $fillable = [
  //   'name',
  //   'email',
  //   'password',
  //   'phone',
  //   'is_verified',
  //   'role'
  // ];

  protected $fillable = [
    'name',
    'email',
    'password',
    'phone',
    'is_verified',
    'role_id',
    'otp_code',
    'otp_expires_at',
    'failed_attempts',
    'locked_until'
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var list<string>
   */
  protected $hidden = [
    'password',
    'remember_token',
    'otp_code'
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  // protected function casts(): array
  // {
  //   return [
  //     'email_verified_at' => 'datetime',
  //     'password' => 'hashed',
  //   ];
  // }
  protected $casts = [
    'is_verified' => 'boolean',
    'otp_expires_at' => 'datetime',
    'locked_until' => 'datetime',
  ];

  public function user_goverment()
  {
    return $this->hasMany(User_Goverment::class, 'user_id');
  }

  public function submittedComplaints()
  {
    return $this->hasMany(Complaint::class);
  }

  public function updatedLogs()
  {
    return $this->hasMany(ComplaintLog::class);
  }

  public function governments()
  {
    return $this->belongsToMany(Government::class, 'user_goverments', 'user_id', 'government_id');
  }


  public function isAdmin()
  {
    return $this->role === 'admin';
  }
  public function isGovernment()
  {
    return $this->role === 'government';
  }
  public function isEmploee()
  {
    return $this->role === 'employee';
  }
  public function isCitizen()
  {
    return $this->role === 'citizen';
  }
}

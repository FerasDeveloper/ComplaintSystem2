<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Government extends Model
{
  protected $fillable = [
    'name',
    'location',
    'description'
  ];

  public function complaint()
  {
    return $this->hasMany(Complaint::class);
  }
  public function user_goverment()
  {
    return $this->hasMany(User_Goverment::class, 'government_id');
  }
  public function users()
  {
    return $this->belongsToMany(User::class, 'user_goverments', 'government_id', 'user_id');
  }
}
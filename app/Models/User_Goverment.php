<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User_Goverment extends Model
{
  protected $fillable = [
    'user_id',
    'government_id',
  ];
  public function user()
  {
    return $this->belongsTo(User::class);
  }

  public function government()
  {
    return $this->belongsTo(Government::class);
  }
}

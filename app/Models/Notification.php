<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
  protected $fillable = ['content', 'user_id'];

  public function user()
  {
    return $this->belongsToMany(User::class);
  }
}

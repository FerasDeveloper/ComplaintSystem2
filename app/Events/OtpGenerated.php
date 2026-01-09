<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OtpGenerated
{
  use Dispatchable, SerializesModels;

  public User $user;
  public string $otp;

  public function __construct(User $user, string $otp)
  {
    $this->user = $user;
    $this->otp = $otp;
  }
}

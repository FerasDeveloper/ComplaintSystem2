<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Complaint;

class ComplaintPolicy
{
  public function update(User $user, Complaint $complaint): bool
  {
    return $user->role_id == 2 || $user->role_id == 3;
  }
}

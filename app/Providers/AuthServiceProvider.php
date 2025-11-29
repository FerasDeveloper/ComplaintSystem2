<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Complaint;
use App\Policies\ComplaintPolicy;

class AuthServiceProvider extends ServiceProvider
{
  protected $policies = [
    Complaint::class => ComplaintPolicy::class,
  ];

  public function boot(): void
  {
    Gate::define('create-employee', function ($user) {
      return $user->role_id == 2;
    });
  }
}

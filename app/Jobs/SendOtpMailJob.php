<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOtpMailJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  public $tries = 3;
  public $timeout = 30;

  protected User $user;
  protected string $text;
  protected string $type;

  public function __construct(User $user, string $text, string $type)
  {
    $this->user = $user;
    $this->text = $text;
    $this->type = $type;
  }

  public function handle(): void
  {
    Mail::raw("{$this->text}", function ($message) {
      $message->to($this->user->email)
        ->subject("{$this->type} Notification");
    });
  }
}

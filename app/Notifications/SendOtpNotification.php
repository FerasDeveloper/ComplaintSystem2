<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;


class SendOtpNotification extends Notification {
  protected $otp;
  public function __construct($otp){ $this->otp = $otp; }
  public function via($notifiable){ return ['mail']; }
  public function toMail($notifiable){
    return (new MailMessage)
      ->subject('رمز التحقق')
      ->line('رمز التحقق الخاص بك: '.$this->otp)
      ->line('ينتهي خلال 10 دقائق');
  }
}

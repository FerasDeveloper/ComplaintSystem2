<?php

namespace App\Listeners;

use App\Events\OtpGenerated;
use App\Jobs\SendOtpMailJob;

class SendOtpListener
{
    public function handle(OtpGenerated $event): void
    {
        SendOtpMailJob::dispatch(
            $event->user,
            "Your verification code is: {$event->otp}",
            'Email Verification Code'
        );
    }
}
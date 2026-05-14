<?php

namespace App\Services;

class NullSmsOtpGateway implements SmsOtpGateway
{
    public function send(string $recipient, string $message): void
    {
        //
    }
}

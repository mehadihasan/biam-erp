<?php

namespace App\Services;

interface SmsOtpGateway
{
    public function send(string $recipient, string $message): void;
}

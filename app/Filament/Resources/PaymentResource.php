<?php

namespace App\Filament\Resources;

use App\Models\Payment;
use Filament\Resources\Resource;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static bool $shouldRegisterNavigation = false;
}

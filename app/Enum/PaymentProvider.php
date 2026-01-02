<?php

namespace App\Enum;

enum PaymentProvider:string
{
    case Stripe ='stripe';
    case Paypal = 'paypal';

    public static function values():array{
        return array_column(self::cases(), 'value');
    }
}

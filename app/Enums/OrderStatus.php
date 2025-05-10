<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Paid = 'paid';
    case Sent = 'sent';
    case Cancelled = 'cancelled';
    case Completed = 'completed';
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HiringList extends Model
{
    use HasFactory;
    protected $fillable = [
        'country',
        'state',
        'city',
        'gender',
        'offer_type',
        'from',
        'to',
        'offer_amount',
        'day_type',
        'accommodation',
        'user_id',
        'number_hires',
        'remark',
        'active_hires',
        'status'
    ];
}

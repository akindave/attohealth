<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KycList extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'identity_front',
        'identity_back',
        'identity_number',
        'message',
        'status'
    ];
    use HasFactory;
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referee extends Model
{
    protected $fillable = [
        'user_id',
        'fullname',
        'email',
        'mobile',
        'status'
    ];
    use HasFactory;
}

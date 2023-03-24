<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceList extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'job_id',
        'date',
        'clock_in_time',
        'clock_out_time',
        'duration',
        'long',
        'lat',
        'note',
        'status'

    ];
}

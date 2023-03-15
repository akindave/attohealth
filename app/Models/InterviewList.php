<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterviewList extends Model
{
    use HasFactory;
    protected $fillable = [
        'interview_time',
        'interview_date',
        'user_id',
        'message',
        'status'
    ];
}

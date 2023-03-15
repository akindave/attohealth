<?php

namespace App\Models;
use Carbon\Carbon;
use ALajusticia\Expirable\Traits\Expirable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OtpCode extends Model
{
    use HasFactory;
    use Expirable;

    protected $fillable = [
        'code','email'
    ];

    public static function defaultExpiresAt()
    {
        return Carbon::now()->addMinutes(10);
    }
}

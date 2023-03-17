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
        'status',
        'distance'
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function state(){
        return $this->belongsTo(State::class,'state')->select(['id','name']);
    }

    public function country(){
        return $this->belongsTo(Country::class,'country')->select(['id','name']);
    }

    public function city(){
        return $this->belongsTo(City::class,'city')->select(['id','name']);
    }

    public function offer_type(){
        return $this->belongsTo(OfferType::class,'offer_type')->select(['id','name']);
    }

}

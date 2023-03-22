<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicantList extends Model
{
    use HasFactory;
    protected $fillable = [
        'applicant_id',
        'job_id',
        'status'
    ];

    public function user(){
        return $this->belongsTo(User::class,'applicant_id');
    }

    public function hiringdetail(){
        return $this->belongsTo(HiringList::class,'job_id')->with('companyinfo');
    }

}

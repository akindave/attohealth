<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'last_name',
        'first_name',
          'username',
          'user_category_id',
             'email',
          'password',
     'mobile_number',
           'country',
             'code',
             'designation',
            'gender',
            'experience_year',
            'education_level',
            'certificate_of_practice',
            'academic_certificate',
            'looking_for',
            'location',
            'state',
            'city',
            'address',
            'interview_time',
            'interview_date',
            'practicing_license',
            'org_logo',
            'name_of_org'



    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

      /**
     * Get all of the comments for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function referer_code(): HasOne
    {
        return $this->hasOne(RefererCode::class);
    }
}

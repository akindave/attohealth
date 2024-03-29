<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();;
            $table->string('mobile_number')->unique();
            $table->string('user_category_id');
            $table->string('gender')->nullable();
            $table->string('designation')->nullable();
            $table->string('address')->nullable();
            $table->string('name_of_org')->nullable();
            $table->string('specialty')->nullable();
            $table->string('experience_year')->nullable();
            $table->string('education_level')->nullable();
            $table->string('looking_for')->nullable();
            $table->string('recommendation')->nullable();
            $table->longText('org_logo')->nullable();
            $table->longText('certificate_of_practice')->nullable();
            $table->longText('academic_certificate')->nullable();
            $table->longText('resume')->nullable();
            $table->longText('practicing_license')->nullable();
            $table->longText('bio')->nullable();
            $table->longText('account_balance')->default(0);
            $table->boolean('isVerified')->default(false);
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('lat')->nullable();
            $table->string('long')->nullable();
            $table->string('code');
            $table->string('profile_pics')->nullable();
            $table->string('job_seeking_status')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->integer('has_pin')->nullable();
            $table->string('transaction_pin')->nullable();
            $table->boolean('Auth2fa')->default(false);
            $table->softDeletes();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

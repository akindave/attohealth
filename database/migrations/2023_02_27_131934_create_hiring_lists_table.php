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
        Schema::create('hiring_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('country');
            $table->string('state');
            $table->string('city');
            $table->string('gender');
            $table->string('offer_type');
            $table->string('from');
            $table->string('to');
            $table->string('offer_amount');
            $table->string('day_type');
            $table->string('accommodation');
            $table->string('number_hires');
            $table->string('active_hires')->nullable();
            $table->string('remark');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hiring_lists');
    }
};

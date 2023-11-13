<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('captain_sallaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId("uid")->references("id")->on("users")->onDelete("cascade");
            $table->integer("sessions_number")->nullable();
            $table->integer("attended_sessions")->nullable();
            $table->integer("absent_sessions")->nullable();
            $table->integer("extra_sessions")->nullable();
            $table->integer("sallary")->nullable();
            $table->string("month")->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('captain_sallaries');
    }
};

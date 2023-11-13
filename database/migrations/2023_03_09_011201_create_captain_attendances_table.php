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
        Schema::create('captain_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId("capID")->references("id")->on("users")->onDelete("cascade");
            $table->foreignId("sessionID")->references("id")->on("captain_schedules");
            $table->string("first_scan")->default("false");
            $table->string("second_scan")->default("false");
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
        Schema::dropIfExists('captain_attendances');
    }
};

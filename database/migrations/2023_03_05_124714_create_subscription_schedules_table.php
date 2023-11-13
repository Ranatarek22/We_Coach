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
        Schema::create('subscription_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId("branchID")->references("id")->on("branches")->onDelete("cascade");
            $table->foreignId("subsID")->references("id")->on("subscription_types")->onDelete("cascade");
            $table->string("day")->nullable();
            $table->string("time")->nullable();
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
        Schema::dropIfExists('subscription_schedules');
    }
};

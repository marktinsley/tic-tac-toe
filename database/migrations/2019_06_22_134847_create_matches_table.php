<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->tinyInteger('type_key');
            $table->unsignedBigInteger('player1_id');
            $table->unsignedBigInteger('player2_id')->nullable()->default(null);
            $table->unsignedBigInteger('winner_id')->nullable()->default(null);
            $table->timestamp('ended_at')->nullable()->default(null);
            $table->timestamps();

            $table->foreign('player1_id')
                ->references('id')->on('users');
            $table->foreign('player2_id')
                ->references('id')->on('users');
            $table->foreign('winner_id')
                ->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('matches');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChaptersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('season_id');
            $table->string('title',150)->nullable(false);
            $table->integer('position')->nullable(false);
            $table->enum('type',['api','url'])->nullable(false)->default('api');
            $table->string('api_code',50)->nullable();
            $table->text('direct_url')->nullable();
            $table->timestamps();

            $table->foreign('season_id')->references('id')->on('seasons')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chapters');
    }
}

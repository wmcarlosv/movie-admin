<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoviesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title',250)->nullable(false);
            $table->text('description')->nullable(false);
            $table->year('year')->nullable(false);
            $table->text('poster')->nullable(false);
            $table->string('api_code',50)->nullable(false);
            $table->integer('views')->nullable()->default(0);
            $table->integer('downloads')->nullable()->default(0);
            $table->enum('status',['A','I'])->nullable(false)->default('A');
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
        Schema::dropIfExists('movies');
    }
}

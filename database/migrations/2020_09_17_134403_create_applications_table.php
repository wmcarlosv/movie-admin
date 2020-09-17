<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('name',100)->nullable(false);
            $table->text('about')->nullable(false);
            $table->string('version',10)->nullable(false);
            $table->text('play_store_url')->nullable();
            $table->text('privacy_policy')->nullable();
            $table->text('url_qualify')->nullable();
            $table->text('url_more_apps')->nullable();
            $table->string('app_code',10)->nullable(false);
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
        Schema::dropIfExists('applications');
    }
}

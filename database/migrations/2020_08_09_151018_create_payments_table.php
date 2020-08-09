<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->integer('user_admin_id');
            $table->unsignedBigInteger('user_client_id');
            $table->enum('payment_method',['mobile_payment','transfer'])->nullable(false);
            $table->double('amount')->nullable(false);
            $table->string('referenceno',100)->nullable(false);
            $table->string('payment_attachment',150)->nullable();
            $table->enum('status',['in_progress','success','failed'])->nullable(false)->default('in_progress');
            $table->timestamps();

            $table->foreign('user_client_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}

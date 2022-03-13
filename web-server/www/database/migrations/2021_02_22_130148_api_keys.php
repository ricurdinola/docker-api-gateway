<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ApiKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_keys', function (Blueprint $table) {
		    $table->bigIncrements('id');
		    $table->unsignedBigInteger('client_key_id');
            $table->unsignedBigInteger('services_id');
            $table->boolean('active')->default(1);
		    $table->softDeletes();
		    $table->timestamps();

            $table->foreign('client_key_id')->references('id')->on('clients_keys');
            $table->foreign('services_id')->references('id')->on('services');
	    });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_keys');
    }
}

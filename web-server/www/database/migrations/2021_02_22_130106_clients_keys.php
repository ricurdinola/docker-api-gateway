<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ClientsKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients_keys', function (Blueprint $table) {
		    $table->bigIncrements('id');
		    $table->unsignedBigInteger('client_id');
		    $table->string('apikey');
		    $table->softDeletes();
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
        Schema::dropIfExists('clients_keys');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Services extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
		    $table->bigIncrements('id');
		    $table->string('service_name');
		    $table->string('description');
            $table->string('method');
            $table->string('in_route');
            $table->string('out_route');
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
        Schema::dropIfExists('services');
    }
}

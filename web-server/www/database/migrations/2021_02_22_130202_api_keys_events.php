<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ApiKeysEvents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_keys_events', function (Blueprint $table) {
		    $table->bigIncrements('id');
            $table->string('in_route');
            $table->string('method')->nullable(); 
            $table->string('apikey')->nullable();                        
		    $table->string('ip_address');
		    $table->timestamp('created_at');//->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at');//->default(DB::raw('CURRENT_TIMESTAMP'));
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_keys_events');
    }
}

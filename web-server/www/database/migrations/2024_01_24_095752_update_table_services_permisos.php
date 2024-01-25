<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTableServicesPermisos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('services', function (Blueprint $table) {
            $table->string('permisos', 1000)->nullable();
            $table->string('login', 1);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared("ALTER TABLE services
                            DROP column permisos,
                            DROP column login;");
    }

}

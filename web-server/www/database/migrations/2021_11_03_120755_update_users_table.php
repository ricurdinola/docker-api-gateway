<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('super_admin')->nullable(false)->default(0);
            $table->tinyInteger('visible')->nullable(false)->default(1);
            $table->bigInteger('updated_by')->unsigned()->nullable(true)->default(null);
            $table->bigInteger('created_by')->unsigned()->nullable(true)->default(null);
            $table->timestamp('deleted_at')->nullable(true)->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('super_admin');
            $table->dropColumn('visible');
            $table->dropColumn('updated_by');
            $table->dropColumn('created_by');
            $table->dropColumn('deleted_at');
        });
    }
}

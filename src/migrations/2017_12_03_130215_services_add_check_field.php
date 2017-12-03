<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ServicesAddCheckField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        capsule_schema()->table('services', function (Blueprint $table) {
            $table->json('check')->default('{}')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        capsule_schema()->table('services', function (Blueprint $table) {
            $table->dropColumn('check');
        });
    }
}

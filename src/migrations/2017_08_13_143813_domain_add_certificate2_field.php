<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DomainAddCertificate2Field extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        capsule_schema()->table('domains', function (Blueprint $table) {
            $table->text('certificate2')->nullable()->after('certificate');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        capsule_schema()->table('domains', function (Blueprint $table) {
            $table->dropColumn('certificate2');
        });
    }
}

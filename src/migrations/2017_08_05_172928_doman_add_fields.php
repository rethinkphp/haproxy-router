<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DomanAddFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        capsule_schema()->table('domains', function (Blueprint $table) {
            $table->string('tls_provider')->notNull()->default('acme');
            $table->json('key_pair')->nullable();
            $table->json('dist_names')->nullable();
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
            $table->dropColumn('tls_provider');
            $table->dropColumn('key_pair');
            $table->dropColumn('dist_names');
        });
    }
}

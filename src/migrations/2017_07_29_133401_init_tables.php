<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InitTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        capsule_schema()->create('services', function (Blueprint $table) {
            $table->string('id')->notNull();

            $table->string('name')->notNull();
            $table->string('description')->notNull()->default('');

            $table->timestamps();

            $table->unique(['name']);
        });

        capsule_schema()->create('nodes', function (Blueprint $table) {
            $table->string('id')->notNull();
            $table->string('service_id')->notNull();

            $table->string('name')->notNull();
            $table->string('host')->notNull();
            $table->unsignedTinyInteger('check')->notNull()->default(0);
            $table->unsignedTinyInteger('backup')->notNull()->default(0);

            $table->timestamps();

            $table->unique(['service_id', 'name']);
        });


        capsule_schema()->create('routes', function (Blueprint $table) {
            $table->string('id')->notNull();
            $table->string('service_id')->notNull();

            $table->string('name')->notNull();
            $table->string('host')->notNull();
            $table->string('path')->notNull()->default('/');

            $table->timestamps();

            $table->unique(['service_id', 'name']);
        });

        capsule_schema()->create('domains', function (Blueprint $table) {
            $table->string('id')->notNull();

            $table->string('name')->notNull();
            $table->string('description')->notNull()->default('');
            $table->string('certificate')->notNull()->default('');

            $table->unsignedTinyInteger('tls_only')->notNull()->default(0);

            $table->timestamps();

            $table->unique(['name']);
        });

        capsule_schema()->create('settings', function (Blueprint $table) {
            $table->string('name')->notNull();
            $table->string('value')->notNull();

            $table->unique(['name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        capsule_schema()->dropIfExists('services');
        capsule_schema()->dropIfExists('nodes');
        capsule_schema()->dropIfExists('routes');
        capsule_schema()->dropIfExists('domains');
        capsule_schema()->dropIfExists('settings');
    }
}

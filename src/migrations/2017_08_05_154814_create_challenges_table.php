<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChallengesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        capsule_schema()->create('challenges', function (Blueprint $table) {
            $table->increments('id');

            $table->string('domain')->notNull();
            $table->string('type')->notNull();
            $table->string('url')->notNull();
            $table->string('token')->notNull();
            $table->string('payload')->notNull();

            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->unique('token');
            $table->unique('domain');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        capsule_schema()->drop('challenges');
    }
}

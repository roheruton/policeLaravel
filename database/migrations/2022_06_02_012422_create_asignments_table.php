<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAsignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asignments', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('police_unit_id')->unsigned();
            $table->foreignId('police_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreign('police_unit_id')->references('id')->on('police_units');
            $table->boolean('estado');
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
        Schema::dropIfExists('asignments');
    }
}

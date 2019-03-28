<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePeriodosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('periodos', function (Blueprint $table) {
            $table->increments('id');
            $table->date('inicio')->nullable();
            $table->date('fim')->nullable();

            $table->integer('periodo_tipo_id')->unsigned();
            $table->integer('protocolo_id')->unsigned();

            $table->timestamps();

            // FK

            $table->foreign('periodo_tipo_id')->references('id')->on('periodo_tipos')->onDelete('cascade');
            $table->foreign('protocolo_id')->references('id')->on('protocolos')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('periodos', function (Blueprint $table) {
            $table->dropForeign('periodos_periodo_tipo_id_foreign');
            $table->dropForeign('periodos_protocolo_id_foreign');
        });

        Schema::dropIfExists('periodos');
    }
}

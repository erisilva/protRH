<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTramitacaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tramitacaos', function (Blueprint $table) {
            $table->increments('id');
            $table->text('descricao')->nullable();
            $table->integer('funcionario_id')->unsigned()->nullable(); // chave fraca
            $table->integer('setor_id')->unsigned()->nullable(); // chave fraca
            $table->integer('user_id')->unsigned(); // quem registrou o protocolo            
            $table->integer('protocolo_id')->unsigned();
            $table->timestamps();

            // fk
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
        Schema::table('tramitacaos', function (Blueprint $table) {
            $table->dropForeign('tramitacaos_user_id_foreign');
            $table->dropForeign('tramitacaos_protocolo_id_foreign');
        });
        Schema::dropIfExists('tramitacaos');
    }
}

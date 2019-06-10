<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOficioTramitacaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oficio_tramitacaos', function (Blueprint $table) {
            $table->increments('id');
            $table->text('descricao')->nullable();
            $table->integer('funcionario_id')->unsigned()->nullable(); // chave fraca
            $table->integer('setor_id')->unsigned()->nullable(); // chave fraca
            $table->integer('user_id')->unsigned(); // quem registrou o protocolo            
            $table->integer('oficio_id')->unsigned();
            $table->timestamps();

            // fk
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('oficio_id')->references('id')->on('oficios')->onDelete('cascade');    
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oficio_tramitacaos', function (Blueprint $table) {
            $table->dropForeign('oficio_tramitacaos_user_id_foreign');
            $table->dropForeign('oficio_tramitacaos_oficio_id_foreign');
        });
        Schema::dropIfExists('oficio_tramitacaos');
    }
}

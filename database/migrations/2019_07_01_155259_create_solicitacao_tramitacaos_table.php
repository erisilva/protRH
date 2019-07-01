<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSolicitacaoTramitacaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitacao_tramitacaos', function (Blueprint $table) {
            $table->increments('id');
            $table->text('descricao')->nullable();
            $table->integer('funcionario_id')->unsigned()->nullable(); // chave fraca
            $table->integer('setor_id')->unsigned()->nullable(); // chave fraca
            $table->integer('user_id')->unsigned(); // quem registrou o protocolo            
            $table->integer('solicitacao_id')->unsigned();
            $table->timestamps();

            // fk
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('solicitacao_id')->references('id')->on('solicitacaos')->onDelete('cascade');  
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('solicitacao_tramitacaos', function (Blueprint $table) {
            $table->dropForeign('solicitacao_tramitacaos_user_id_foreign');
            $table->dropForeign('solicitacao_tramitacaos_solicitacao_id_foreign');
        });        
        Schema::dropIfExists('solicitacao_tramitacaos');
    }
}

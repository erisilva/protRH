<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProtocolosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('protocolos', function (Blueprint $table) {
            $table->increments('id');
            $table->text('descricao')->nullable();
            $table->integer('funcionario_id')->unsigned();
            $table->integer('setor_id')->unsigned();
            
            $table->integer('grupo_id')->unsigned(); // default 1 // grupo a ser encaminhado
            $table->dateTime('encaminhado_em')->nullable(); // data e hora do encaminhamento
            
            $table->integer('protocolo_tipo_id')->unsigned();
            $table->integer('protocolo_situacao_id')->unsigned();

            $table->text('concluido_mensagem')->nullable();
            $table->enum('concluido', ['s', 'n']); // defaul n
            $table->dateTime('concluido_em')->nullable();
            $table->integer('resposta_id')->unsigned(); // default 1, sem resposta ainda


            $table->integer('user_id')->unsigned(); // quem registrou o protocolo
            $table->string('chave', 20)->unique();
            $table->timestamps();

            // FK
            $table->foreign('funcionario_id')->references('id')->on('funcionarios')->onDelete('cascade');
            $table->foreign('setor_id')->references('id')->on('setors')->onDelete('cascade');
            $table->foreign('grupo_id')->references('id')->on('grupos')->onDelete('cascade');
            $table->foreign('resposta_id')->references('id')->on('respostas')->onDelete('cascade');
            $table->foreign('protocolo_tipo_id')->references('id')->on('protocolo_tipos')->onDelete('cascade');
            $table->foreign('protocolo_situacao_id')->references('id')->on('protocolo_situacaos')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('protocolos', function (Blueprint $table) {
            $table->dropForeign('protocolos_funcionario_id_foreign');
            $table->dropForeign('protocolos_setor_id_foreign');
            $table->dropForeign('protocolos_grupo_id_foreign');
            $table->dropForeign('protocolos_resposta_id_foreign');
            $table->dropForeign('protocolos_protocolo_tipo_id_foreign');
            $table->dropForeign('protocolos_protocolo_situacao_id_foreign');
            $table->dropForeign('protocolos_user_id_foreign');
        });
        
        Schema::dropIfExists('protocolos');
    }
}

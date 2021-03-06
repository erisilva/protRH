<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSolicitacaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('solicitacaos', function (Blueprint $table) {
            $table->increments('id');
            $table->text('remetente')->nullable();
            $table->text('observacao')->nullable();
            $table->string('identificacao')->nullable();
            $table->string('chave', 20)->unique();
            $table->integer('solicitacao_tipo_id')->unsigned();
            $table->integer('solicitacao_situacao_id')->unsigned();
            $table->integer('user_id')->unsigned(); // quem registrou o protocolo

            $table->integer('grupo_id')->unsigned(); // default 1 // grupo a ser encaminhado
            $table->dateTime('encaminhado_em')->nullable(); // data e hora do encaminhamento

            $table->text('concluido_mensagem')->nullable();
            $table->enum('concluido', ['s', 'n']); // defaul n
            $table->dateTime('concluido_em')->nullable();
            $table->integer('resposta_id')->unsigned(); // default 1, sem resposta ainda

            $table->timestamps();

            // FK
            $table->foreign('grupo_id')->references('id')->on('grupos')->onDelete('cascade');
            $table->foreign('resposta_id')->references('id')->on('respostas')->onDelete('cascade');
            $table->foreign('solicitacao_tipo_id')->references('id')->on('solicitacao_tipos')->onDelete('cascade');
            $table->foreign('solicitacao_situacao_id')->references('id')->on('solicitacao_situacaos')->onDelete('cascade');
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
        Schema::table('solicitacaos', function (Blueprint $table) {
            $table->dropForeign('solicitacaos_solicitacao_tipo_id_foreign');
            $table->dropForeign('solicitacaos_solicitacao_situacao_id_foreign');
            $table->dropForeign('solicitacaos_user_id_foreign');
            $table->dropForeign('solicitacaos_grupo_id_foreign');
            $table->dropForeign('solicitacaos_resposta_id_foreign');
        });        
        Schema::dropIfExists('solicitacaos');
    }
}

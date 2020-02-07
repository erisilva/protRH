<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemorandosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('memorandos', function (Blueprint $table) {
            $table->increments('id');
            $table->text('remetente')->nullable();
            $table->text('observacao')->nullable();
            $table->string('chave', 20)->unique();
            $table->integer('memorando_tipo_id')->unsigned();
            $table->integer('memorando_situacao_id')->unsigned();
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
            $table->foreign('memorando_tipo_id')->references('id')->on('memorando_tipos')->onDelete('cascade');
            $table->foreign('memorando_situacao_id')->references('id')->on('memorando_situacaos')->onDelete('cascade');
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
        Schema::table('memorandos', function (Blueprint $table) {
            $table->dropForeign('memorandos_memorando_tipo_id_foreign');
            $table->dropForeign('memorandos_memorando_situacao_id_foreign');
            $table->dropForeign('memorandos_user_id_foreign');
            $table->dropForeign('memorandos_grupo_id_foreign');
            $table->dropForeign('memorandos_resposta_id_foreign');
        });

        Schema::dropIfExists('memorandos');
    }
}

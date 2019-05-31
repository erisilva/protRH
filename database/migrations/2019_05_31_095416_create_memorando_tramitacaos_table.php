<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMemorandoTramitacaosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('memorando_tramitacaos', function (Blueprint $table) {
            $table->increments('id');

            $table->text('descricao')->nullable();
            $table->integer('funcionario_id')->unsigned()->nullable(); // chave fraca
            $table->integer('setor_id')->unsigned()->nullable(); // chave fraca
            $table->integer('user_id')->unsigned(); // quem registrou o protocolo            
            $table->integer('memorando_id')->unsigned();
            $table->timestamps();

            // fk
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('memorando_id')->references('id')->on('memorandos')->onDelete('cascade');            
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('memorando_tramitacaos', function (Blueprint $table) {
            $table->dropForeign('memorando_tramitacaos_user_id_foreign');
            $table->dropForeign('memorando_tramitacaos_memorando_id_foreign');
        });
        Schema::dropIfExists('memorando_tramitacaos');
    }
}

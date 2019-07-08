<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnexosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('anexos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('arquivoNome'); // nome do arquivo
            $table->string('arquivoLocal'); // pasta onde será salvo o arquivo
            $table->text('arquivoUrl'); // url completa do arquivo
            
            $table->integer('anexoable_id');
            $table->string('anexoable_type');

            $table->integer('user_id')->unsigned();
            $table->timestamps();

            //fk
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
        Schema::table('anexos', function (Blueprint $table) {
            $table->dropForeign('anexos_user_id_foreign');
        });
        Schema::dropIfExists('anexos');
    }
}

<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['prefix' => 'admin','namespace' => 'Auth'],function(){
    // Authentication Routes...
    Route::get('login', 'LoginController@showLoginForm')->name('login');
    Route::post('login', 'LoginController@login');
    Route::post('logout', 'LoginController@logout')->name('logout');
});

Route::get('/home', 'HomeController@index')->name('home');

Route::prefix('admin')->namespace('Admin')->group(function () {
	/*  Operadores */
	// nota mental :: as rotas extras devem ser declaradas antes de se declarar as rotas resources
    Route::get('/users/password', 'ChangePasswordController@showPasswordUpdateForm')->name('users.password');
	Route::put('/users/password/update', 'ChangePasswordController@passwordUpdate')->name('users.passwordupdate');
    Route::get('/users/export/csv', 'UserController@exportcsv')->name('users.export.csv');
	Route::get('/users/export/pdf', 'UserController@exportpdf')->name('users.export.pdf');
    Route::resource('/users', 'UserController');

	/* Permissões */
    Route::get('/permissions/export/csv', 'PermissionController@exportcsv')->name('permissions.export.csv');
	Route::get('/permissions/export/pdf', 'PermissionController@exportpdf')->name('permissions.export.pdf');
    Route::resource('/permissions', 'PermissionController');

    /* Perfis */
    Route::get('/roles/export/csv', 'RoleController@exportcsv')->name('roles.export.csv');
    Route::get('/roles/export/pdf', 'RoleController@exportpdf')->name('roles.export.pdf');
    Route::resource('/roles', 'RoleController');
});


/* Funcionarios */
Route::get('/funcionarios/export/csv', 'FuncionarioController@exportcsv')->name('funcionarios.export.csv');
Route::get('/funcionarios/export/pdf', 'FuncionarioController@exportpdf')->name('funcionarios.export.pdf');
Route::get('/funcionarios/autocomplete', 'FuncionarioController@autocomplete')->name('funcionarios.autocomplete');
Route::resource('/funcionarios', 'FuncionarioController');

/* Setores */
Route::get('/setores/export/csv', 'SetorController@exportcsv')->name('setores.export.csv');
Route::get('/setores/export/pdf', 'SetorController@exportpdf')->name('setores.export.pdf');
Route::get('/setores/autocomplete', 'SetorController@autocomplete')->name('setores.autocomplete');
Route::resource('/setores', 'SetorController');

/* Tipificação dos protocolos */
Route::get('/protocolotipos/export/csv', 'ProtocoloTipoController@exportcsv')->name('protocolotipos.export.csv');
Route::get('/protocolotipos/export/pdf', 'ProtocoloTipoController@exportpdf')->name('protocolotipos.export.pdf');
Route::resource('/protocolotipos', 'ProtocoloTipoController');

/* Situacões ou status dos protocolos */
Route::get('/protocolosituacoes/export/csv', 'ProtocoloSituacaoController@exportcsv')->name('protocolosituacoes.export.csv');
Route::get('/protocolosituacoes/export/pdf', 'ProtocoloSituacaoController@exportpdf')->name('protocolosituacoes.export.pdf');
Route::resource('/protocolosituacoes', 'ProtocoloSituacaoController');

/* Tipos de períodos */
Route::get('/periodotipos/export/csv', 'PeriodoTipoController@exportcsv')->name('periodotipos.export.csv');
Route::get('/periodotipos/export/pdf', 'PeriodoTipoController@exportpdf')->name('periodotipos.export.pdf');
Route::resource('/periodotipos', 'PeriodoTipoController');

/* grupos (de trabalho) */
Route::get('/grupos/export/csv', 'GrupoController@exportcsv')->name('grupos.export.csv');
Route::get('/grupos/export/pdf', 'GrupoController@exportpdf')->name('grupos.export.pdf');
Route::resource('/grupos', 'GrupoController');

/* Respostas aos pedidos (protocolos, memorandos, solicitações e ofícios) */
Route::get('/respostas/export/csv', 'RespostaController@exportcsv')->name('respostas.export.csv');
Route::get('/respostas/export/pdf', 'RespostaController@exportpdf')->name('respostas.export.pdf');
Route::resource('/respostas', 'RespostaController');

/* PROTOCOLOS */
Route::get('/protocolos/export/csv', 'ProtocoloController@exportcsv')->name('protocolos.export.csv');
Route::get('/protocolos/export/pdf', 'ProtocoloController@exportpdf')->name('protocolos.export.pdf');
Route::get('/protocolos/export/pdf/porsetor', 'ProtocoloController@exportpdfporsetor')->name('protocolos.export.porsetor.pdf');
Route::get('/protocolos/export/pdf/porsetor/simples', 'ProtocoloController@exportpdfporsetorsimples')->name('protocolos.export.porsetor.simples.pdf');
Route::get('/protocolos/export/pdf/{id}/individual', 'ProtocoloController@exportpdfindividual')->name('protocolos.export.pdf.individual');
Route::get('/protocolos/export/pdf/encaminhamento', 'ProtocoloController@exportpdfencaminhamento')->name('protocolos.export.encaminhamento.pdf');
Route::get('/protocolos/{chave}/buscar', 'ProtocoloPublicoController@buscar')->name('protocolos.chave.buscar');
Route::post('/protocolos/concluir/{id}', 'ProtocoloController@concluir')->name('protocolos.concluir');
Route::post('/protocolos/encaminhar/{id}', 'ProtocoloController@encaminhar')->name('protocolos.encaminhar');
Route::resource('/protocolos', 'ProtocoloController');

/*PERIODOS*/
Route::resource('/periodos', 'PeriodoController')->only(['store', 'destroy',]);

/*TRAMITAÇÕES DOS PROTOCOLOS*/
Route::resource('/tramitacoes', 'TramitacaoController')->only(['store', 'destroy',]);

/*ANEXOS DOS PROTOCOLOS*/
Route::resource('/protocoloanexos', 'ProtocoloAnexoController')->only(['store', 'destroy',]);

/* Tipificação dos memorandos */
Route::get('/memorandotipos/export/csv', 'MemorandoTipoController@exportcsv')->name('memorandotipos.export.csv');
Route::get('/memorandotipos/export/pdf', 'MemorandoTipoController@exportpdf')->name('memorandotipos.export.pdf');
Route::resource('/memorandotipos', 'MemorandoTipoController');

/* Situacões ou status dos memorandos */
Route::get('/memorandosituacoes/export/csv', 'MemorandoSituacaoController@exportcsv')->name('memorandosituacoes.export.csv');
Route::get('/memorandosituacoes/export/pdf', 'MemorandoSituacaoController@exportpdf')->name('memorandosituacoes.export.pdf');
Route::resource('/memorandosituacoes', 'MemorandoSituacaoController');

/* MEMORANDOS */
Route::get('/memorandos/export/csv', 'MemorandoController@exportcsv')->name('memorandos.export.csv');
Route::get('/memorandos/export/pdf', 'MemorandoController@exportpdf')->name('memorandos.export.pdf');
Route::get('/memorandos/export/pdf/{id}/individual', 'MemorandoController@exportpdfindividual')->name('memorandos.export.pdf.individual');
Route::get('/memorandos/{chave}/buscar', 'MemorandoPublicoController@buscar')->name('memorandos.chave.buscar');
Route::post('/memorandos/concluir/{id}', 'MemorandoController@concluir')->name('memorandos.concluir');
Route::post('/memorandos/encaminhar/{id}', 'MemorandoController@encaminhar')->name('memorandos.encaminhar');
Route::resource('/memorandos', 'MemorandoController');

/*TRAMITAÇÕES DOS MEMORANDOS*/
Route::resource('/memorandotramitacoes', 'MemorandoTramitacaoController')->only(['store', 'destroy',]);

/*ANEXOS DOS MEMORANDO*/
Route::resource('/memorandoanexos', 'MemorandoAnexoController')->only(['store', 'destroy',]);

/* Tipificação dos ofícios */
Route::get('/oficiotipos/export/csv', 'OficioTipoController@exportcsv')->name('oficiotipos.export.csv');
Route::get('/oficiotipos/export/pdf', 'OficioTipoController@exportpdf')->name('oficiotipos.export.pdf');
Route::resource('/oficiotipos', 'OficioTipoController');

/* Situacões ou status dos ofícios */
Route::get('/oficiosituacoes/export/csv', 'OficioSituacaoController@exportcsv')->name('oficiosituacoes.export.csv');
Route::get('/oficiosituacoes/export/pdf', 'OficioSituacaoController@exportpdf')->name('oficiosituacoes.export.pdf');
Route::resource('/oficiosituacoes', 'OficioSituacaoController');

/* OFÍCIOS */
Route::get('/oficios/export/csv', 'OficioController@exportcsv')->name('oficios.export.csv');
Route::get('/oficios/export/pdf', 'OficioController@exportpdf')->name('oficios.export.pdf');
Route::get('/oficios/export/pdf/{id}/individual', 'OficioController@exportpdfindividual')->name('oficios.export.pdf.individual');
Route::get('/oficios/{chave}/buscar', 'OficioPublicoController@buscar')->name('oficios.chave.buscar');
Route::post('/oficios/concluir/{id}', 'OficioController@concluir')->name('oficios.concluir');
Route::post('/oficios/encaminhar/{id}', 'OficioController@encaminhar')->name('oficios.encaminhar');
Route::resource('/oficios', 'OficioController');

/*TRAMITAÇÕES DOS OFÍCIOS*/
Route::resource('/oficiotramitacoes', 'OficioTramitacaoController')->only(['store', 'destroy',]);

/*TRAMITAÇÕES DOS ANEXOS*/
Route::resource('/oficioanexos', 'OficioAnexoController')->only(['store', 'destroy',]);

/* Tipificação das solicitações */
Route::get('/solicitacaotipos/export/csv', 'SolicitacaoTipoController@exportcsv')->name('solicitacaotipos.export.csv');
Route::get('/solicitacaotipos/export/pdf', 'SolicitacaoTipoController@exportpdf')->name('solicitacaotipos.export.pdf');
Route::resource('/solicitacaotipos', 'SolicitacaoTipoController');

/* Situacões ou status das solicitações */
Route::get('/solicitacaosituacoes/export/csv', 'SolicitacaoSituacaoController@exportcsv')->name('solicitacaosituacoes.export.csv');
Route::get('/solicitacaosituacoes/export/pdf', 'SolicitacaoSituacaoController@exportpdf')->name('solicitacaosituacoes.export.pdf');
Route::resource('/solicitacaosituacoes', 'SolicitacaoSituacaoController');

/* SOLICITAÇÕES */
Route::get('/solicitacoes/export/csv', 'SolicitacaoController@exportcsv')->name('solicitacoes.export.csv');
Route::get('/solicitacoes/export/pdf', 'SolicitacaoController@exportpdf')->name('solicitacoes.export.pdf');
Route::get('/solicitacoes/export/pdf/{id}/individual', 'SolicitacaoController@exportpdfindividual')->name('solicitacoes.export.pdf.individual');
Route::get('/solicitacoes/{chave}/buscar', 'SolicitacaoPublicoController@buscar')->name('solicitacoes.chave.buscar');
Route::post('/solicitacoes/concluir/{id}', 'SolicitacaoController@concluir')->name('solicitacoes.concluir');
Route::post('/solicitacoes/encaminhar/{id}', 'SolicitacaoController@encaminhar')->name('solicitacoes.encaminhar');
Route::resource('/solicitacoes', 'SolicitacaoController');

/*TRAMITAÇÕES DOS SOLICITAÇÕES*/
Route::resource('/solicitacaotramitacoes', 'SolicitacaoTramitacaoController')->only(['store', 'destroy',]);

/*TRAMITAÇÕES DOS ANEXOS*/
Route::resource('/solicitacaoanexos', 'SolicitacaoAnexoController')->only(['store', 'destroy',]);
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

/* PROTOCOLOS */
Route::get('/protocolos/export/csv', 'ProtocoloController@exportcsv')->name('protocolos.export.csv');
Route::get('/protocolos/export/pdf', 'ProtocoloController@exportpdf')->name('protocolos.export.pdf');
Route::get('/protocolos/export/pdf/{id}/individual', 'ProtocoloController@exportpdfindividual')->name('protocolos.export.pdf.individual');
Route::resource('/protocolos', 'ProtocoloController');

/*PERIODOS*/
Route::resource('/periodos', 'PeriodoController')->only(['store', 'destroy',]);

/*TRAMITAÇÕES*/
Route::resource('/tramitacoes', 'TramitacaoController')->only(['store', 'destroy',]);
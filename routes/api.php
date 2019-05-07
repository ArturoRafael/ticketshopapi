<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



Route::apiResource('genero','GeneroController');
Route::apiResource('artista','ArtistController');
Route::apiResource('temporada','TemporadaController');

Route::get('imagen','ImagenController@index');
Route::get('imagen/{imagen}','ImagenController@show');
Route::post('imagen','ImagenController@store');
Route::post('imagen/{imagen}','ImagenController@update');
Route::delete('imagen/{imagen}','ImagenController@destroy');


Route::apiResource('auditorio','AuditorioController');
Route::apiResource('imagenesauditorio','ImagenesAuditorioController');
Route::apiResource('tipodescuento','TipoDescuentoController');
Route::apiResource('cupon','CuponController');
Route::apiResource('tipocupon','TipoCuponController');
Route::apiResource('cuponera','CuponeraController');
Route::apiResource('tipoevento','TipoEventoController');
Route::apiResource('evento','EventoController');
Route::apiResource('imagenevento','ImagenEventoController');
Route::apiResource('imagenartist','ImagenArtistController');
Route::apiResource('puntoventaevento','PuntoventaEventoController');
Route::apiResource('eventocuponera','EventoCuponeraController');
Route::apiResource('cliente','ClienteController');
Route::apiResource('palco','PalcoController');
Route::apiResource('fila','FilaController');
Route::apiResource('puesto','PuestoController');
Route::apiResource('puestospalco','PuestosPalcoController');
Route::apiResource('localidad','LocalidadController');
Route::apiResource('tribuna','TribunaController');
Route::apiResource('puntoventum','PuntoVentumController');
Route::apiResource('grupsvendedore','GrupsVendedoreController');
Route::apiResource('grupovendedorespto','GrupoVendedoresPtoController');

Route::get('listeventipo/{listeventipo}','EventoController@listeventipo');
Route::get('detalle_evento/{detalle_evento}','EventoController@detalle_evento');
Route::get('buscar_evento','EventoController@buscar_evento');
Route::get('listadoartistevento','ArtistController@listadoartistevento');



Route::get('listausuarios', 'UsuarioController@listausuarios');

Route::get('comprasrealizadas/{comprasrealizadas}', 'UsuarioController@comprasrealizadas');
Route::get('temporadascompradas/{temporadascompradas}', 'UsuarioController@temporadascompradas');
Route::get('reservas/{reservas}', 'UsuarioController@reservas');


Route::post('login', 'UsuarioController@login');
Route::post('register', 'UsuarioController@register');

Route::delete('destroy', 'UsuarioController@destroy');

Route::get('signup/activate/{token}', 'UsuarioController@signupActivate');

Route::get('auth/{provider}', 'UsuarioController@redirectToProvider');
Route::get('auth/{provider}/callback', 'UsuarioController@handleProviderCallback');

Route::group(['middleware' => 'auth:api'], function () { 
	Route::post('cambioclave', 'UsuarioController@cambioclave');
	Route::post('detailsuser', 'UsuarioController@detailsuser');
	Route::put('updateprofile/{updateprofile}', 'UsuarioController@updateprofile');
	
	Route::post('logout', 'UsuarioController@logout');

	Route::post('create', 'PasswordResetController@create');
    Route::get('find/{token}', 'PasswordResetController@find');
    Route::post('reset', 'PasswordResetController@reset');

});


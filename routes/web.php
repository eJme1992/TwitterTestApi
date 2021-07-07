<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

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


//RUTAS DEL API
Route::post('api/user/login','UserController@login');
Route::post('api/user/register', [UserController::class, 'register']);
Route::put('api/user/update','UserController@update')->middleware(App\Http\Middleware\ApiAuthMiddleware::class);
//Clase aplicando api.Auth
Route::post('api/user/register/fileUser/{tipo}','UserController@fileUser')->middleware(App\Http\Middleware\ApiAuthMiddleware::class);
Route::post('api/user/register/fileUser/{tipo}/{post}','UserController@fileUser')->middleware(App\Http\Middleware\ApiAuthMiddleware::class);

Route::get('api/user/getfileuser/{slug_user}','UserController@getFileUser');
Route::get('api/getfile/{filename}','UserController@getFile');
Route::get('api/getusuario/{slug_user}','UserController@getUsuario');
// Verifica si el correo exite o no al momento de ser registrado
Route::get('api/user/validate/email/{email}','UserController@ValidateEmail');
Route::get('api/user/validate/username/{username}','UserController@ValidateUserName');

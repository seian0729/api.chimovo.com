<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\AccountController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\ditbloxDataController;
use App\Http\Controllers\AccountSlotController;
use App\Http\Controllers\HaneiController;
use App\Models\Data;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

use Illuminate\Support\Facades\DB;

$router->get('/', function () use ($router) {
    return 'Ài Lái Chùm';
});
// v1/users create group prefix

$router->group(['prefix' => 'v1/users'], function () use ($router) {
    $router->post('login', 'AccountController@login');
    $router->post('loginKey','AccountController@loginKey');
    $router->get('getTotalSlot','AccountSlotController@getTotalSlot');
});

$router->get('/v1/user', 'AccountController@getUser');
$router->group(['prefix' => 'v1/data'], function () use ($router) {
    $router->get('getData', 'DataController@getData');
    $router->get('getDataChunk', 'DataController@getDataChunk');
    $router->get('getDataLimit', 'DataController@getDataLimit');
    $router->get('getTotalAccount', 'DataController@getTotalAccount');
    $router->get('getDataByUsername', 'DataController@getDataByUsername');
    $router->get('getDataByUsernameAndGameId', 'DataController@getDataByUsernameAndGameId');
    $router->get('getDataByUsernames', 'DataController@getDataByUsernames');
    $router->get('getOnlineAccountEachNote', 'DataController@getOnlineAccountEachNote');
    $router->post('updateData', 'DataController@updateData');
    $router->post('deleteData', 'DataController@deleteData');
    $router->post('bulkDeleteData', 'DataController@bulkDeleteData');
    // bulkUpdatePasswordAndCookie
    $router->post('bulkUpdatePasswordAndCookie', 'DataController@bulkUpdatePasswordAndCookie');
});
$router->group(['prefix' => 'v1/Petx'], function () use ($router) {
    $router->post('getData', 'PetxsendController@getData');
    $router->post('updateData', 'PetxsendController@updateData');
    $router->post('createData', 'PetxsendController@createData');
    $router->get('getOrder', 'PetxsendController@getOrder');
    $router->get('getRate','PetxsendController@getRate');
});

$router->group(['prefix' => 'v1/admin'], function () use ($router) {
    $router->get('GetUsers', 'AdminController@getAllUsers');
});

//
$router->group(['prefix' => 'v1/admin', 'middleware' => 'admin'], function () use ($router) {
    $router->get('/', function () use ($router) {
        return 'Admin';
    });
});
//key
$router->group(['prefix' => 'v1/hanei'], function () use ($router) {
    $router->get('getSignature','HaneiController@getSignature');
    $router->get('script','HaneiController@getScript');
});
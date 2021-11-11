<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('api/produk', 'ProdukController@tampil');
$router->get('api/produk/{id}', 'ProdukController@detail');
$router->post('api/produk', 'ProdukController@tambah');
$router->put('api/produk/{id}', 'ProdukController@ubah');
$router->delete('api/produk/{id}', 'ProdukController@hapus');
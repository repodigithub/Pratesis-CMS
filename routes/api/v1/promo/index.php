<?php

$router->group(['prefix' => 'promo'], function () use ($router) {
  $router->get('/generate-id', ['uses' => 'PromoController@generateID']);
  $router->get('/', ['uses' => 'PromoController@index']);
  $router->post('/', ['uses' => 'PromoController@create']);

  $router->group(['prefix' => '{id}'], function () use ($router) {
    $router->get('/', ['uses' => 'PromoController@show']);
    $router->put('/', ['uses' => 'PromoController@update']);
    $router->delete('/', ['uses' => 'PromoController@delete']);
    $router->put('/status', ['uses' => 'PromoController@updateStatus']);

    include('image.php');
    include('product.php');
    include('area.php');
  });
});

$router->group(['prefix' => 'promo-depot'], function () use ($router) {
  $router->get('/', ['uses' => 'PromoAreaController@index']);

  $router->group(['prefix' => '{id}'], function () use ($router) {
    $router->get('/', ['uses' => 'PromoAreaController@show']);
    $router->get('/image', ['uses' => 'PromoImageController@index']);
    $router->get('/product', ['uses' => 'PromoProductController@index']);
    $router->get('/product/{product}', ['uses' => 'PromoProductController@show']);
    $router->put('/status', ['uses' => 'PromoAreaController@updateStatus']);

    include('distributor.php');
  });
});

$router->group(['prefix' => 'promo-distributor'], function () use ($router) {
  $router->get('/', ['uses' => 'PromoDistributorController@index']);

  $router->group(['prefix' => '{id}'], function () use ($router) {
    $router->get('/', ['uses' => 'PromoDistributorController@show']);
    $router->get('/image', ['uses' => 'PromoImageController@index']);
    $router->get('/product', ['uses' => 'PromoProductController@index']);
    $router->get('/product/{product}', ['uses' => 'PromoProductController@show']);
    $router->put('/status', ['uses' => 'PromoDistributorController@updateStatus']);
  });
});

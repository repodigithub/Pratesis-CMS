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

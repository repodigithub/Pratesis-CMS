<?php

$router->group(['prefix' => 'promo'], function () use ($router) {
  $router->get('/generate-id', ['uses' => 'PromoController@generateID']);
  $router->get('/', ['uses' => 'PromoController@index']);
  $router->post('/', ['uses' => 'PromoController@create']);
  $router->get('/{id}', ['uses' => 'PromoController@show']);
  $router->put('/{id}', ['uses' => 'PromoController@update']);
  $router->put('/{id}/status', ['uses' => 'PromoController@updateStatus']);
  $router->delete('/{id}', ['uses' => 'PromoController@delete']);
});

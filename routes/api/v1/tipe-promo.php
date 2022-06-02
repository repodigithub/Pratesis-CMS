<?php

$router->group(['prefix' => 'tipe-promo'], function () use ($router) {
  $router->get('/', ['uses' => 'TipePromoController@index']);
  $router->post('/', ['uses' => 'TipePromoController@create']);
  $router->post('/upload', ['uses' => 'TipePromoController@upload']);
  $router->get('/{id}', ['uses' => 'TipePromoController@show']);
  $router->put('/{id}', ['uses' => 'TipePromoController@update']);
  $router->delete('/{id}', ['uses' => 'TipePromoController@delete']);
});

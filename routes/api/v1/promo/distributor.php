<?php

$router->group(['prefix' => 'distributor'], function () use ($router) {
  $router->get('/', ['uses' => 'PromoDistributorController@index']);
  $router->post('/', ['uses' => 'PromoDistributorController@create']);
  $router->get('/{dis}', ['uses' => 'PromoDistributorController@show']);
  $router->put('/{dis}', ['uses' => 'PromoDistributorController@update']);
  $router->delete('/{dis}', ['uses' => 'PromoDistributorController@delete']);
  $router->post('/delete', ['uses' => 'PromoDistributorController@deleteBatch']);
});

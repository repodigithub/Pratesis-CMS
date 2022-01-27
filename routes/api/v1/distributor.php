<?php

$router->group(['prefix' => 'distributor'], function () use ($router) {
  $router->get('/', ['uses' => 'DistributorController@index']);
  $router->post('/', ['uses' => 'DistributorController@create']);
  $router->post('/upload', ['uses' => 'DistributorController@upload']);
  $router->get('/{id}', ['uses' => 'DistributorController@show']);
  $router->put('/{id}', ['uses' => 'DistributorController@update']);
});

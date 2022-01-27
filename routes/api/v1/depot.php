<?php

$router->group(['prefix' => 'depot'], function () use ($router) {
  $router->get('/', ['uses' => 'DepotController@index']);
  $router->post('/', ['uses' => 'DepotController@create']);
  $router->post('/upload', ['uses' => 'DepotController@upload']);
  $router->get('/{id}', ['uses' => 'DepotController@show']);
  $router->put('/{id}', ['uses' => 'DepotController@update']);
});

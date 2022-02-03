<?php

$router->group(['prefix' => 'area'], function () use ($router) {
  $router->get('/', ['uses' => 'AreaController@index']);
  $router->post('/', ['uses' => 'AreaController@create']);
  $router->post('/upload', ['uses' => 'AreaController@upload']);
  $router->get('/{id}', ['uses' => 'AreaController@show']);
  $router->put('/{id}', ['uses' => 'AreaController@update']);
  $router->delete('/{id}', ['uses' => 'AreaController@delete']);
});

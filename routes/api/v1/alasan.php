<?php

$router->group(['prefix' => 'alasan'], function () use ($router) {
  $router->get('/', ['uses' => 'AlasanController@index']);
  $router->post('/', ['uses' => 'AlasanController@create']);
  $router->post('/upload', ['uses' => 'AlasanController@upload']);
  $router->get('/{id}', ['uses' => 'AlasanController@show']);
  $router->put('/{id}', ['uses' => 'AlasanController@update']);
  $router->delete('/{id}', ['uses' => 'AlasanController@delete']);
});

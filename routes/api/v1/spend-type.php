<?php

$router->group(['prefix' => 'spend-type'], function () use ($router) {
  $router->get('/', ['uses' => 'SpendTypeController@index']);
  $router->post('/', ['uses' => 'SpendTypeController@create']);
  $router->post('/upload', ['uses' => 'SpendTypeController@upload']);
  $router->get('/{id}', ['uses' => 'SpendTypeController@show']);
  $router->put('/{id}', ['uses' => 'SpendTypeController@update']);
  $router->delete('/{id}', ['uses' => 'SpendTypeController@delete']);
});

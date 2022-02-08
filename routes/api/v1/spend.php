<?php

$router->group(['prefix' => 'spend'], function () use ($router) {
  $router->get('/', ['uses' => 'SpendController@index']);
  $router->post('/', ['uses' => 'SpendController@create']);
  $router->post('/upload', ['uses' => 'SpendController@upload']);
  $router->get('/{id}', ['uses' => 'SpendController@show']);
  $router->put('/{id}', ['uses' => 'SpendController@update']);
  $router->delete('/{id}', ['uses' => 'SpendController@delete']);
});

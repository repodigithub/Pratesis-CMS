<?php

$router->group(['prefix' => 'category'], function () use ($router) {
  $router->get('/', ['uses' => 'CategoryController@index']);
  $router->post('/', ['uses' => 'CategoryController@create']);
  $router->post('/upload', ['uses' => 'CategoryController@upload']);
  $router->get('/{id}', ['uses' => 'CategoryController@show']);
  $router->put('/{id}', ['uses' => 'CategoryController@update']);
  $router->delete('/{id}', ['uses' => 'CategoryController@delete']);
});

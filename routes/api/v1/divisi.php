<?php

$router->group(['prefix' => 'divisi'], function () use ($router) {
  $router->get('/', ['uses' => 'DivisiController@index']);
  $router->post('/', ['uses' => 'DivisiController@create']);
  $router->post('/upload', ['uses' => 'DivisiController@upload']);
  $router->get('/{id}', ['uses' => 'DivisiController@show']);
  $router->put('/{id}', ['uses' => 'DivisiController@update']);
  $router->delete('/{id}', ['uses' => 'DivisiController@delete']);
});

<?php

$router->group(['prefix' => 'sub-brand'], function () use ($router) {
  $router->get('/', ['uses' => 'SubBrandController@index']);
  $router->post('/', ['uses' => 'SubBrandController@create']);
  $router->post('/upload', ['uses' => 'SubBrandController@upload']);
  $router->get('/{id}', ['uses' => 'SubBrandController@show']);
  $router->put('/{id}', ['uses' => 'SubBrandController@update']);
  $router->delete('/{id}', ['uses' => 'SubBrandController@delete']);
});

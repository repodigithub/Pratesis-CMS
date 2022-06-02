<?php

$router->group(['prefix' => 'brand'], function () use ($router) {
  $router->get('/', ['uses' => 'BrandController@index']);
  $router->post('/', ['uses' => 'BrandController@create']);
  $router->post('/upload', ['uses' => 'BrandController@upload']);
  $router->get('/{id}', ['uses' => 'BrandController@show']);
  $router->put('/{id}', ['uses' => 'BrandController@update']);
  $router->delete('/{id}', ['uses' => 'BrandController@delete']);
});

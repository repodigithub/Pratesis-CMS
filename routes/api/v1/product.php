<?php

$router->group(['prefix' => 'product'], function () use ($router) {
  $router->get('/', ['uses' => 'ProductController@index']);
  $router->post('/', ['uses' => 'ProductController@create']);
  $router->post('/upload', ['uses' => 'ProductController@upload']);
  $router->get('/{id}', ['uses' => 'ProductController@show']);
  $router->put('/{id}', ['uses' => 'ProductController@update']);
  $router->delete('/{id}', ['uses' => 'ProductController@delete']);
});

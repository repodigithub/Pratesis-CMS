<?php

$router->group(['prefix' => 'tax'], function () use ($router) {
  $router->get('/', ['uses' => 'TaxController@index']);
  $router->post('/', ['uses' => 'TaxController@create']);
  $router->post('/upload', ['uses' => 'TaxController@upload']);
  $router->get('/{id}', ['uses' => 'TaxController@show']);
  $router->put('/{id}', ['uses' => 'TaxController@update']);
  $router->delete('/{id}', ['uses' => 'TaxController@delete']);
});

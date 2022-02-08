<?php

$router->group(['prefix' => 'investment'], function () use ($router) {
  $router->get('/', ['uses' => 'InvestmentController@index']);
  $router->post('/', ['uses' => 'InvestmentController@create']);
  $router->post('/upload', ['uses' => 'InvestmentController@upload']);
  $router->get('/{id}', ['uses' => 'InvestmentController@show']);
  $router->put('/{id}', ['uses' => 'InvestmentController@update']);
  $router->delete('/{id}', ['uses' => 'InvestmentController@delete']);
});

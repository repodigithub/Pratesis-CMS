<?php

$router->group(['prefix' => 'claim'], function () use ($router) {
  $router->get('/', ['uses' => 'ClaimController@index']);
  $router->post('/', ['uses' => 'ClaimController@create']);
  $router->post('/upload', ['uses' => 'ClaimController@upload']);
  $router->get('/{id}', ['uses' => 'ClaimController@show']);
  $router->get('/{id}/invoice', ['uses' => 'ClaimController@showInvoice']);
  $router->put('/{id}', ['uses' => 'ClaimController@update']);
  $router->delete('/{id}', ['uses' => 'ClaimController@delete']);
});

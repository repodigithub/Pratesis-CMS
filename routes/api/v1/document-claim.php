<?php

$router->group(['prefix' => 'document-claim'], function () use ($router) {
  $router->get('/', ['uses' => 'DocumentClaimController@index']);
  $router->post('/', ['uses' => 'DocumentClaimController@create']);
  $router->post('/upload', ['uses' => 'DocumentClaimController@upload']);
  $router->get('/{id}', ['uses' => 'DocumentClaimController@show']);
  $router->put('/{id}', ['uses' => 'DocumentClaimController@update']);
  $router->delete('/{id}', ['uses' => 'DocumentClaimController@delete']);
});

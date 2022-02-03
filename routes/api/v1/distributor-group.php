<?php

$router->group(['prefix' => 'distributor-group'], function () use ($router) {
  $router->get('/', ['uses' => 'DistributorGroupController@index']);
  $router->post('/', ['uses' => 'DistributorGroupController@create']);
  $router->post('/upload', ['uses' => 'DistributorGroupController@upload']);
  $router->get('/{id}', ['uses' => 'DistributorGroupController@show']);
  $router->put('/{id}', ['uses' => 'DistributorGroupController@update']);
  $router->delete('/{id}', ['uses' => 'DistributorGroupController@delete']);
});

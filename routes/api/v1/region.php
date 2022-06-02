<?php

$router->group(['prefix' => 'region'], function () use ($router) {
  $router->get('/', ['uses' => 'RegionController@index']);
  $router->post('/', ['uses' => 'RegionController@create']);
  $router->post('/upload', ['uses' => 'RegionController@upload']);
  $router->get('/{id}', ['uses' => 'RegionController@show']);
  $router->put('/{id}', ['uses' => 'RegionController@update']);
  $router->delete('/{id}', ['uses' => 'RegionController@delete']);
});

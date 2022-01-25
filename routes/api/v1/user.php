<?php

$router->group(['prefix' => 'user'], function () use ($router) {
  $router->get('/', ['uses' => 'UserController@index']);
  $router->post('/action', ['uses' => 'UserController@action']);
  $router->get('/{id}', ['uses' => 'UserController@show']);
  $router->put('/{id}', ['uses' => 'UserController@update']);
});

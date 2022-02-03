<?php

$router->group(['prefix' => 'user-group'], function () use ($router) {
  $router->get('/', ['uses' => 'UserGroupController@index']);
  $router->post('/', ['uses' => 'UserGroupController@create']);
  $router->get('/{id}', ['uses' => 'UserGroupController@show']);
  $router->put('/{id}', ['uses' => 'UserGroupController@update']);
  $router->delete('/{id}', ['uses' => 'UserGroupController@delete']);
  $router->put('/{id}/attach', ['uses' => 'UserGroupController@attach']);
  $router->put('/{id}/detach', ['uses' => 'UserGroupController@detach']);
});

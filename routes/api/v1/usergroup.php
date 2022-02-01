<?php

$router->group(['prefix' => 'usergroup'], function () use ($router) {
  $router->get('/', ['uses' => 'UsergroupController@index']);
});

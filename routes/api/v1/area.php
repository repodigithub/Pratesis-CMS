<?php

$router->group(['prefix' => 'area'], function () use ($router) {
  $router->get('/', ['uses' => 'AreaController@index']);
});

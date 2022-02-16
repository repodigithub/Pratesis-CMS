<?php

$router->group(['prefix' => 'master-data'], function () use ($router) {
  $router->get('/', ['uses' => 'MasterDataController@index']);
  $router->get('/{type}', ['uses' => 'MasterDataController@indexType']);
});

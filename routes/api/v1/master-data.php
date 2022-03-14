<?php

$router->group(['prefix' => 'master-data'], function () use ($router) {
  $router->get('/', ['uses' => 'MasterDataController@index']);
  $router->post('/download', ['uses' => 'MasterDataController@download']);
  $router->post('/delete', ['uses' => 'MasterDataController@deleteBatch']);
  $router->get('/{type}', ['uses' => 'MasterDataController@indexType']);
  $router->delete('/{id}', ['uses' => 'MasterDataController@delete']);
});

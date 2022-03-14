<?php

$router->group(['prefix' => 'area'], function () use ($router) {
  $router->get('/', ['uses' => 'PromoAreaController@index']);
  $router->post('/', ['uses' => 'PromoAreaController@create']);
  $router->get('/{area}', ['uses' => 'PromoAreaController@show']);
  $router->put('/{area}', ['uses' => 'PromoAreaController@update']);
  $router->delete('/{area}', ['uses' => 'PromoAreaController@delete']);
  $router->post('/delete', ['uses' => 'PromoAreaController@deleteBatch']);
});

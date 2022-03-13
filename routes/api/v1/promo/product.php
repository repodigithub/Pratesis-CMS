<?php

$router->group(['prefix' => 'product'], function () use ($router) {
  $router->get('/', ['uses' => 'PromoProductController@index']);
  $router->post('/', ['uses' => 'PromoProductController@create']);
  $router->get('/{product}', ['uses' => 'PromoProductController@show']);
  $router->put('/{product}', ['uses' => 'PromoProductController@update']);
  $router->delete('/{product}', ['uses' => 'PromoProductController@delete']);
});

<?php

$router->group(['prefix' => 'image'], function () use ($router) {
  $router->get('/', ['uses' => 'PromoImageController@index']);
  $router->post('/', ['uses' => 'PromoImageController@create']);
  $router->delete('/{image}', ['uses' => 'PromoImageController@delete']);
});

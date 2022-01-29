<?php

$router->group(['prefix' => 'auth'], function () use ($router) {
  $router->post('register', ['uses' => 'AuthController@register']);
  $router->post('login', ['uses' => 'AuthController@login']);
  $router->post('forget-password', ['uses' => 'AuthController@forgetPassword']);
  $router->post('reset-password/{token}', ['uses' => 'AuthController@resetPassword']);
  $router->get('me', ['uses' => 'AuthController@me']);
  $router->put('profile', ['uses' => 'AuthController@updateProfile']);
});

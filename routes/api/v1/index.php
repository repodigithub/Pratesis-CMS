<?php

$router->group(['prefix' => 'v1'], function () use ($router) {
  include('auth.php');
  include('user.php');
  include('depot.php');
  include('distributor.php');
});

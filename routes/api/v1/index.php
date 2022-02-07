<?php

$router->group(['prefix' => 'v1'], function () use ($router) {
  include('area.php');
  include('auth.php');
  include('distributor-group.php');
  include('distributor.php');
  include('sub-brand.php');
  include('region.php');
  include('user-group.php');
  include('user.php');
});

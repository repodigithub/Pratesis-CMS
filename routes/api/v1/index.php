<?php

$router->group(['prefix' => 'v1'], function () use ($router) {
  include('area.php');
  include('auth.php');
  include('distributor-group.php');
  include('distributor.php');
  include('product.php');
  include('region.php');
  include('sub-brand.php');
  include('user-group.php');
  include('user.php');
});

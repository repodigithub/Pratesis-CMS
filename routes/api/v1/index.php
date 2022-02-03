<?php

$router->group(['prefix' => 'v1'], function () use ($router) {
  include('area.php');
  include('auth.php');
  include('depot.php');
  include('distributor-group.php');
  include('distributor.php');
  include('region.php');
  include('user-group.php');
  include('user.php');
});

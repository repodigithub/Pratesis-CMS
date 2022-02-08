<?php

$router->group(['prefix' => 'v1'], function () use ($router) {
  include('alasan.php');
  include('area.php');
  include('auth.php');
  include('brand.php');
  include('budget-holder.php');
  include('category.php');
  include('distributor-group.php');
  include('distributor.php');
  include('divisi.php');
  include('document-claim.php');
  include('investment.php');
  include('product.php');
  include('region.php');
  include('sub-brand.php');
  include('user-group.php');
  include('user.php');
});

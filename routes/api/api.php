<?php

$router->group(['prefix' => 'api'], function () use ($router) {
  include('v1/index.php');
});

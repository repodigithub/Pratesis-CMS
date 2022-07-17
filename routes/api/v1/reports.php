<?php

$router->group(['prefix' => 'report'], function () use ($router) {
  $router->get('/ho/promo', ['uses' => 'ReportController@promo']);
  $router->get('/ga/claim', ['uses' => 'ReportController@claim']);
});

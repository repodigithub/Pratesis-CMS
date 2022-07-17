<?php

$router->group(['prefix' => 'report'], function () use ($router) {
  $router->get('/ho/promo', ['uses' => 'ReportController@promo']);
  $router->get('/ga/claim', ['uses' => 'ReportController@claim']);
  $router->get('/ga/list-claim', ['uses' => 'ReportController@listClaim']);
  $router->get('/ga/list-opso', ['uses' => 'ReportController@listOpso']);
  $router->get('/ga/list-promo', ['uses' => 'ReportController@listPromo']);
});

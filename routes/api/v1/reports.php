<?php

$router->group(['prefix' => 'report'], function () use ($router) {
  $router->group(['prefix' => 'ho'], function () use ($router) {
    $router->get('/promo', ['uses' => 'ReportController@promo']);
    $router->get('/area', ['uses' => 'ReportController@area']);
    $router->get('/brand', ['uses' => 'ReportController@brand']);

    $router->post('/promo', ['uses' => 'ReportController@getReportPromoByHo']);
    $router->post('/area', ['uses' => 'ReportController@getReportAreaByHo']);
    $router->post('/brand', ['uses' => 'ReportController@getReportBrandByHo']);
  });
});

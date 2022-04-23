<?php

$router->group(['prefix' => 'dashboard'], function () use ($router) {
  $router->get('/mini-data', ['uses' => 'DashboardController@getMiniData']);
  $router->get('/by-divisi', ['uses' => 'DashboardController@getByDivisi']);
  $router->get('/by-brand', ['uses' => 'DashboardController@getByBrand']);
  $router->get('/by-region', ['uses' => 'DashboardController@getByRegion']);
  $router->get('/by-area', ['uses' => 'DashboardController@getByArea']);

  $router->get('/tidak-layak-bayar', ['uses' => 'DashboardController@getTidakLayakBayar']);
  $router->get('/menunggu-pembayaran', ['uses' => 'DashboardController@getMenungguPembayaran']);
});

<?php

$router->group(['prefix' => 'setting'], function () use ($router) {
    $router->get('/note', ['uses' => 'SettingController@getNote']);
    $router->put('/note', ['uses' => 'SettingController@updateNote']);
    $router->get('/sign', ['uses' => 'SettingController@getSign']);
    $router->put('/sign', ['uses' => 'SettingController@updateSign']);
    $router->get('/invoice-note', ['uses' => 'SettingController@getInvoiceNote']);
});

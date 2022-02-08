<?php

$router->group(['prefix' => 'budget-holder'], function () use ($router) {
  $router->get('/', ['uses' => 'BudgetHolderController@index']);
  $router->post('/', ['uses' => 'BudgetHolderController@create']);
  $router->post('/upload', ['uses' => 'BudgetHolderController@upload']);
  $router->get('/{id}', ['uses' => 'BudgetHolderController@show']);
  $router->put('/{id}', ['uses' => 'BudgetHolderController@update']);
  $router->delete('/{id}', ['uses' => 'BudgetHolderController@delete']);
});

<?php

$app->get('/', 'App\Controllers\HomeController:dispatch')->setName('homepage');

$app->group('/api/v1', function() use ($app){

    $app->get('/conversations[/{start}[/{limit}[/{filterString}[/{startDate}[/{endDate}]]]]]','App\Controllers\ApiController:conversations');

    $app->get('/ping','App\Controllers\ApiController:ping');

    $app->post('/authenticate','App\Controllers\ApiController:authenticate');

});
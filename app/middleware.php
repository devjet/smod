<?php

$app->add(new \Slim\Middleware\HttpBasicAuthentication([
    "path" => "/api/v1/token",
    "secure" => false,
    "relaxed" => ["localhost, smod"],
    "users" => [
        "test" => "test"
    ]
]));


$app->add(new \Slim\Middleware\JwtAuthentication([
    "path" => $container['token']->getSetting('path'), // "/api",
    "secure" =>  $container['token']->getSetting('secure'), //  false,
    "relaxed" => $container['token']->getSetting('relaxed'), // ["localhost", "smod"],
    "passthrough" =>  $container['token']->getSetting('passthrough'), //["/api/v1/token", "/api/v1/ping"],
    "secret" =>  $container['token']->getSetting('secret'),
    "error" => function ($request, $response, $arguments) {
        $data["status"] = "error";
        $data["message"] = $arguments["message"];
        return $response->withHeader("Content-Type", "application/json")
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    },
    "logger" => $container['logger'],
    "callback" => function ($request, $response, $arguments) use ($container) {
       $container["token"]->assign($arguments["decoded"]);
    }
]));



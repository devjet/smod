<?php
use \App\Models\Token;

$container = $app->getContainer();

$container['renderer'] = function ($c) {
    $settings = $c->get('settings')['renderer'];
    return new Slim\Views\PhpRenderer($settings['template_path']);
};

$container['logger'] = function ($c) {
    $settings = $c->get('settings')['logger'];
    $logger = new Monolog\Logger($settings['name']);
    $logger->pushProcessor(new Monolog\Processor\UidProcessor());
    $logger->pushHandler(new Monolog\Handler\StreamHandler($settings['path'], Monolog\Logger::DEBUG));
    return $logger;
};


$container['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        $data = [
            'code' => $exception->getCode(),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => explode("\n", $exception->getTraceAsString()),
        ];

        return $c->get('response')->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($data));
    };
};


# Controllers


$container['App\Controllers\ApiController'] = function ($c) {
    return new App\Controllers\ApiController(
        $c->get('token'),
        $c->get('logger')
    );
};

$container['App\Controllers\HomeController'] = function ($c) {
    return new App\Controllers\HomeController(
        $c->get('logger')
    );
};


# Models


$container['token'] = function ($c) {
    $setting = $c->get('settings')['jwt'];
    return new Token($setting);
};

# Repositories


$container['App\Repositories\ApiRepository'] = function ($c) {
    return new App\Repositories\ApiRepository();
};


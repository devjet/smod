<?php

namespace App\Controllers;

use App\Models\Token;
use Slim\Http\Request;
use Slim\Http\Response;
use Psr\Log\LoggerInterface;


final class ApiController
{
    private $logger;
    private $_token;

    public function __construct(\App\Models\Token $token, LoggerInterface $logger)
    {
        $this->_token = $token;
        $this->logger = $logger;
    }


    public function conversations(Request $request, Response $response, $args)
    {

        if (false === $this->_token->hasAccessTo(
                Token::ACCESS_ONE_DEVICE_READ,
                Token::ACCESS_ONE_DEVICE_READ_WRITE,
                Token::ACCESS_ALL_DEVICES_READ_WRITE)
        ) {
            throw new \Exception("Token not allowed to read conversations.", 403);
        }

        $this->logger->info("Conversations action should be dispatched here..");

        return $response->withJson('Wow, token-auth successful! ');
    }

    public function ping(Request $request, Response $response, $args)
    {
        return $response->withJson('pong');
    }

    public function authenticate(Request $request, Response $response, $args)
    {
        return $this->_token->authenticate($request, $response);

    }


}
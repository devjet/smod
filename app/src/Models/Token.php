<?php

namespace App\Models;

use Slim\Http\Request;
use Slim\Http\Response;
use Firebase\JWT\JWT;
use Tuupola\Base62;

class Token
{

    private $_data;
    private $_tokenSettings;
    private $_defaultScopes;

    const ACCESS_ONE_DEVICE_READ = 'scope.R_ONE';
    const ACCESS_ONE_DEVICE_READ_WRITE = 'scope.RW_ONE';
    const ACCESS_ALL_DEVICES_READ_WRITE = 'scope.RW_ALL';

    public function __construct($tokenSetting)
    {
        $this->_tokenSettings = $tokenSetting;
        $this->_defaultScopes = [
            self::ACCESS_ONE_DEVICE_READ,
            self::ACCESS_ONE_DEVICE_READ_WRITE,
            self::ACCESS_ALL_DEVICES_READ_WRITE
        ];

    }

    public function assign($decoded)
    {
        $this->_data = $decoded;
    }

    public function getSetting($setting)
    {
        if (array_key_exists($setting, $this->_tokenSettings))
            return $this->_tokenSettings[$setting];
        else
            throw new \Exception('Setting for token not found');
    }

    private function _hasScope(array $scope)
    {
        return !!count(array_intersect($scope, $this->_data->scope));
    }

    public function hasAccessTo($type)
    {
        $rightsContainer = [];
        if (func_num_args() > 0) {
            $args = func_get_args();
            foreach ($args as $arg){

                if(!is_array($arg)){
                    $rightsContainer[] = $arg;
                }else{
                    $rightsContainer = $arg;
                }
            }
            $rightsContainer = array_unique($rightsContainer);
        }

        return $this->_hasScope($rightsContainer);
    }

    public function authenticate(Request $request, Response $response)
    {

        $now = new \DateTime();
        $future = new \DateTime("now +2 hours");
        $server = $request->getServerParams();
        $jti = Base62::encode(random_bytes(16));

        $payload = [
            "iat" => $now->getTimeStamp(),
            "exp" => $future->getTimeStamp(),
            "jti" => $jti,
            "sub" => $server["PHP_AUTH_USER"],
            "scope" => $this->_defaultScopes
        ];

        $secret = $this->getSetting('secret');
        $token = JWT::encode($payload, $secret, "HS256");
        $data["status"] = "ok";
        $data["token"] = $token;

        return $response->withStatus(201)
            ->withHeader("Content-Type", "application/json")
            ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

    }

}
<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production

        //PostgreSQL
        'db' =>[
            'host'=>'192.168.32.140',
            'database'=>'smod',
            'username'=>'postgres',
            'password'=>'',
            'logging'=>true
        ],
        //Monolog
        'logger' => [
            'name' => 'slim-app',
            'path' => PATH_ROOT . '../storage/log/server.log',
        ],

        'jwt' => [
            'secret' => "Jis0sjsjs9Sdf65f6%Fsa123koOksoallZqpOFBEQED",
            'passthrough'=> ["/api/v1/authenticate", "/api/v1/ping"],
            'relaxed'=> ["localhost", "smod"],
            "path" => "/api",
            "secure" => false,
        ]
    ],
];

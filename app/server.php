<?php
require PATH_ROOT . '/../vendor/autoload.php';

session_start();

$settings = require PATH_ROOT . '/../config/setting.php';
$app = new \Slim\App($settings);

require PATH_ROOT . '../app/dependency.php';

require PATH_ROOT . '../app/middleware.php';

require PATH_ROOT . '../app/route.php';

require PATH_ROOT . '/../config/database.php';

$app->run();
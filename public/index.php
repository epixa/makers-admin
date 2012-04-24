<?php

define('ROOT_PATH', dirname(dirname(__FILE__)));

require ROOT_PATH . '/vendors/Slim/Slim/Slim.php';
$app = new Slim();

require ROOT_PATH . '/src/config.php';
require ROOT_PATH . '/src/mongo.php';
require ROOT_PATH . '/src/routes.php';

$app->run();

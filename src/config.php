<?php

$app->config(array(
    'debug' => true,
    'log.enable' => true,
    'templates.path' => ROOT_PATH . '/templates',
    'mongo.server' => 'mongodb://localhost:27017',
    'mongo.options' => array()
));

$app->configureMode('production', function() use ($app) {
    $app->config(array(
        'mongo.options' => array('username' => '', 'password' => '') + $app->config('mongo.options')
    ));
});

$app->configureMode('development', function() use ($app) {
    $app->config(array(
        'log.enable' => false
    ));
});

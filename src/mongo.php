<?php

$app->mongo = new Mongo($app->config('mongo.server'), $app->config('mongo.options'));
$app->mongo = $app->mongo->makers;

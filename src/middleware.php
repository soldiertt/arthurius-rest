<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);
$corsOptions = array(
    "origin" => "www.local.dev:3000"
);
$cors = new \CorsSlim\CorsSlim($corsOptions);
$app->add(new \CorsSlim\CorsSlim());
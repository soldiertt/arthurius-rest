<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);
$corsOptions = array(
    "origin" => "http://www.local.test:4200",
    "allowMethods" => array("GET,POST,PUT,DELETE,PATCH")
);
$cors = new \CorsSlim\CorsSlim($corsOptions);
$app->add($cors);
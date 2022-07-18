<?php

use Arthurius\model\AdvancedProduct;

$app->get('/advancedproduct', function ($request, $response, $args) {

    $this->logger->info("Slim-Skeleton '/advancedproduct'");
    $products = AdvancedProduct::findAll();

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($products));
});


<?php

use Arthurius\model\AdvancedProduct;
use Arthurius\model\Authorization;
use Arthurius\model\Uploader;

$app->get('/advancedproduct', function ($request, $response, $args) {

    $this->logger->info("Slim-Skeleton '/advancedproduct'");
    $products = AdvancedProduct::findAll();

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($products));
});

$app->post('/advancedproduct', function ($request, $response, $args) {

    if (!Authorization::checkIsAdmin($request)) {
        return Authorization::forbidden($response);
    }

    $product = $request->getParsedBody();

    $this->logger->info("Slim-Skeleton 'post /advancedproduct/");
    $id = AdvancedProduct::create($product);

    $product['id'] = $id;

    return $response->withStatus(201)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($product));
});

$app->post('/advancedproduct/upload', function ($request, $response, $args) {
    $this->logger->info("Slim-Skeleton 'post /advancedproduct/upload'");

    if (!Authorization::checkIsAdmin($request)) {
        return Authorization::forbidden($response);
    }

    $ok = Uploader::upload($request, Uploader::PRODUCT_TYPE);

    if ($ok) {
        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array('status' => $ok)));
    } else {
        return $response->withStatus(500)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode(array('status' => $ok)));
    }
});

$app->delete('/advancedproduct/{id}', function ($request, $response, $args) {

    if (!Authorization::checkIsAdmin($request)) {
        return Authorization::forbidden($response);
    }

    $id = $request->getAttribute('id');

    $this->logger->info("Slim-Skeleton 'delete /advancedproduct/".$id);
    $ok = AdvancedProduct::delete($id);

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($ok));
});

$app->put('/advancedproduct/{id}', function ($request, $response, $args) {

    if (!Authorization::checkIsAdmin($request)) {
        return Authorization::forbidden($response);
    }

    $id = $request->getAttribute('id');
    $product = $request->getParsedBody();

    $this->logger->info("Slim-Skeleton 'put /advancedproduct/".$id);

    $ok = AdvancedProduct::update($id, $product);

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($ok));
});
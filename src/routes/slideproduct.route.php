<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 11-02-17
 * Time: 22:48
 */

use Arthurius\model\SlideProduct;
use Arthurius\model\Authorization;

$app->get('/slideproduct', function ($request, $response, $args) {
    $this->logger->info("Slim-Skeleton '/slideproduct");
    $products = SlideProduct::findAll();

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($products));

});

$app->post('/slideproduct', function ($request, $response, $args) {

    if (!Authorization::checkIsAdmin($request)) {
        return Authorization::forbidden($response);
    }

    $product = $request->getParsedBody();

    $this->logger->info("Slim-Skeleton 'post /product/");
    $id = SlideProduct::create($product);

    $product['id'] = $id;

    return $response->withStatus(201)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($product));
});

$app->delete('/slideproduct/{id}', function ($request, $response, $args) {

    if (!Authorization::checkIsAdmin($request)) {
        return Authorization::forbidden($response);
    }

    $id = $request->getAttribute('id');

    $this->logger->info("Slim-Skeleton 'delete /product/".$id);
    $ok = SlideProduct::delete($id);

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($ok));
});
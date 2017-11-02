<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 11-02-17
 * Time: 22:48
 */

use Arthurius\model\Brand;
use Arthurius\model\Authorization;

$app->get('/brand', function ($request, $response, $args) {
    $this->logger->info("Slim-Skeleton 'get /brand '");

    $brands = Brand::all();

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($brands));
});

$app->post('/brand', function ($request, $response, $args) {

    if (!Authorization::checkIsAdmin($request)) {
        return Authorization::forbidden($response);
    }

    $brand = $request->getParsedBody();
    $this->logger->info("Slim-Skeleton 'post /brand '".json_encode($brand));

    $id = Brand::create($brand);

    $brand['id'] = $id;

    return $response->withStatus(201)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($brand));
});

$app->delete('/brand/{id}', function ($request, $response, $args) {

    if (!Authorization::checkIsAdmin($request)) {
        return Authorization::forbidden($response);
    }

    $id = $request->getAttribute('id');
    $this->logger->info("Slim-Skeleton 'delete /brand '".$id);

    $ok = Brand::delete($id);

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($ok));
});
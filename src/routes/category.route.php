<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 11-02-17
 * Time: 22:49
 */


use Arthurius\model\Category;
use Arthurius\model\Authorization;

$app->get('/category', function ($request, $response, $args) {
    $this->logger->info("Slim-Skeleton 'get /category");
    $categories = Category::allRoots();

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($categories));
});

$app->get('/category/all', function ($request, $response, $args) {

    if (!Authorization::checkIsAdmin($request)) {
        return Authorization::forbidden($response);
    }

    $this->logger->info("Slim-Skeleton 'get /category/all");
    $categories = Category::all();

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($categories));
});

$app->get('/category/name/{type}', function ($request, $response, $args) {
    $categoryName = $request->getAttribute('type');
    $this->logger->info("Slim-Skeleton 'get /category/name/".$categoryName);
    $category = Category::findByName($categoryName);

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($category));
});

$app->get('/category/{parent}', function ($request, $response, $args) {
    $parentCateg = $request->getAttribute('parent');
    $this->logger->info("Slim-Skeleton 'get /category/".$parentCateg);
    $categories = Category::subCategories($parentCateg);

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($categories));
});

$app->post('/category', function ($request, $response, $args) {

    if (!Authorization::checkIsAdmin($request)) {
        return Authorization::forbidden($response);
    }

    $category = $request->getParsedBody();

    $this->logger->info("Slim-Skeleton 'post /category/");

    $id = Category::create($category);

    $category['id'] = $id;

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($category));
});

$app->put('/category/{id}', function ($request, $response, $args) {

    if (!Authorization::checkIsAdmin($request)) {
        return Authorization::forbidden($response);
    }

    $id = $request->getAttribute('id');
    $category = $request->getParsedBody();

    $this->logger->info("Slim-Skeleton 'put /category/".$id);
    $ok = Category::update($id, $category);

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($ok));
});

$app->delete('/category/{id}', function ($request, $response, $args) {

    if (!Authorization::checkIsAdmin($request)) {
        return Authorization::forbidden($response);
    }

    $id = $request->getAttribute('id');

    $this->logger->info("Slim-Skeleton 'delete /category/".$id);
    $ok = Category::delete($id);

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($ok));
});
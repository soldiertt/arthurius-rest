<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 11-02-17
 * Time: 22:49
 */


use Arthurius\model\Category;


$app->get('/category', function ($request, $response, $args) {
    $this->logger->info("Slim-Skeleton '/category");
    $categories = Category::allRoots();

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($categories));
});


$app->get('/category/name/{type}', function ($request, $response, $args) {
    $categoryName = $request->getAttribute('type');
    $this->logger->info("Slim-Skeleton '/category/name/".$categoryName);
    $category = Category::findByName($categoryName);

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($category));
});

$app->get('/category/{parent}', function ($request, $response, $args) {
    $parentCateg = $request->getAttribute('parent');
    $this->logger->info("Slim-Skeleton '/category/".$parentCateg);
    $categories = Category::subCategories($parentCateg);

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($categories));
});

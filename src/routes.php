<?php
// Routes

use Arthurius\model\Product;
use Arthurius\model\Category;

$app->get('/product', function ($request, $response, $args) {
    $category = $request->getQueryParam("category");
    $this->logger->info("Slim-Skeleton '/product/".$category);
    $products = Product::findByCategory($category);

    return $response->withStatus(200)
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($products));
});

$app->get('/product/search', function ($request, $response, $args) {
    $term = $request->getQueryParam("term");
    $this->logger->info("Slim-Skeleton '/product/search");
    $products = Product::search($term);

    return $response->withStatus(200)
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($products));
});

$app->get('/product/promo', function ($request, $response, $args) {
    $this->logger->info("Slim-Skeleton '/product/promo");
    $products = Product::findPromo();

    return $response->withStatus(200)
    ->withHeader('Content-Type', 'application/json')
    ->write(json_encode($products));

});

$app->get('/product/{id}', function ($request, $response, $args) {
    $id = $request->getAttribute('id');
    $this->logger->info("Slim-Skeleton '/product/".$id);
    $product = Product::find($id);
    if ($product === false) {
        return notFound($response, "Could not find product with id ".$id);
    } else {
        return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($product));
    }
});

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

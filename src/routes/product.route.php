<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 11-02-17
 * Time: 22:48
 */

use Arthurius\model\Product;

$app->get('/product', function ($request, $response, $args) {
    $category = $request->getQueryParam("category");
    $brand = $request->getQueryParam("brand");
    $this->logger->info("Slim-Skeleton '/product'");
    if ($category) {
        $products = Product::findByCategory($category);
    } else if ($brand) {
        $products = Product::findByBrand($brand);
    }
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

$app->get('/product/brands', function ($request, $response, $args) {
    $this->logger->info("Slim-Skeleton '/product/brands");
    $brands = Product::findAllBrands();

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($brands));

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
<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 11-02-17
 * Time: 22:48
 */

use Arthurius\model\Product;
use Arthurius\model\TopSales;
use Arthurius\model\Authorization;

$app->get('/product', function ($request, $response, $args) {
    $category = $request->getQueryParam("category");
    $brand = $request->getQueryParam("brand");
    $this->logger->info("Slim-Skeleton '/product'");
    if ($category) {
        $products = Product::findByCategory($category);
    } else if ($brand) {
        $products = Product::findByBrand($brand);
    } else {
        $products = Product::findAll();
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

$app->get('/product/slider', function ($request, $response, $args) {
    $this->logger->info("Slim-Skeleton '/product/slider");
    $products = Product::findSlider();

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

$app->get('/product/top', function ($request, $response, $args) {
    $category = $request->getQueryParam("category");
    $this->logger->info("Slim-Skeleton '/product/top'");
    $products = Product::findTopSalesByCategory($category);

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($products));
});

$app->post('/product/top', function ($request, $response, $args) {
    $orders = $request->getParsedBody();
    $this->logger->info("Slim-Skeleton 'post /product/top '".json_encode($orders));

    $ok = TopSales::updateTopSales($orders);

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($ok));
});

$app->get('/product/{id}', function ($request, $response, $args) {
    $id = $request->getAttribute('id');
    $this->logger->info("Slim-Skeleton 'get /product/".$id);
    $product = Product::find($id);
    if ($product === false) {
        return notFound($response, "Could not find product with id ".$id);
    } else {
        return $response->withStatus(200)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($product));
    }
});

$app->post('/product', function ($request, $response, $args) {

    if (!Authorization::checkIsAdmin($request)) {
        return Authorization::forbidden($response);
    }

    $product = $request->getParsedBody();

    $this->logger->info("Slim-Skeleton 'post /product/");
    $id = Product::create($product);

    $product['id'] = $id;

    return $response->withStatus(201)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($product));
});

$app->put('/product/{id}', function ($request, $response, $args) {

    if (!Authorization::checkIsAdmin($request)) {
        return Authorization::forbidden($response);
    }

    $id = $request->getAttribute('id');
    $product = $request->getParsedBody();

    $this->logger->info("Slim-Skeleton 'put /product/".$id);

    $ok = Product::update($id, $product);

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($ok));
});

$app->delete('/product/{id}', function ($request, $response, $args) {

    if (!Authorization::checkIsAdmin($request)) {
        return Authorization::forbidden($response);
    }

    $id = $request->getAttribute('id');

    $this->logger->info("Slim-Skeleton 'delete /product/".$id);
    $ok = Product::delete($id);

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($ok));
});
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
use Arthurius\model\Uploader;
use Arthurius\model\Exporter;

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

$app->get('/product/export', function ($request, $response, $args) {
    $this->logger->info("Slim-Skeleton 'get /product/export'");

    if (!Authorization::checkIsAdmin($request)) {
        return Authorization::forbidden($response);
    }

    $category = $request->getQueryParam("category");
    $brand = $request->getQueryParam("brand");
    $steel = $request->getQueryParam("steel");
    $promo = $request->getQueryParam("promo");
    $instock = $request->getQueryParam("instock");

    $fileName = 'exp_products_'.date('Ymd_His').'.csv';

    $csvString = Exporter::export($category, $brand, $steel, $promo, $instock);

    $response->getBody()->write($csvString);
    $response = $response->withHeader('Content-Description', 'File Transfer')
        ->withHeader('Access-Control-Expose-Headers', 'Content-Disposition')
        ->withHeader('Content-Type', 'text/csv')
        ->withHeader('Content-Disposition', "attachment;filename=$fileName");

    return $response;
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

$app->post('/product/upload', function ($request, $response, $args) {
    $this->logger->info("Slim-Skeleton 'post /product/upload'");

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

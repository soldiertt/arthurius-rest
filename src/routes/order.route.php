<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 11-02-17
 * Time: 22:48
 */

use Arthurius\model\Order;

$app->post('/order', function ($request, $response, $args) {
    $order = $request->getParsedBody();
    $this->logger->info("Slim-Skeleton 'post /order '".json_encode($order));

    $id = Order::createOrder($order);

    $order['id'] = $id;

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($order));
});


$app->get('/order', function ($request, $response, $args) {
    $userId = $request->getQueryParam('userId');
    $this->logger->info("Slim-Skeleton 'get /order '".json_encode($userId));

    $orders = Order::allOrdersByUser($userId);

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($orders));
});
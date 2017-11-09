<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 11-02-17
 * Time: 22:48
 */

use Arthurius\model\Steel;
use Arthurius\model\Authorization;

$app->get('/steel', function ($request, $response, $args) {
    $this->logger->info("Slim-Skeleton 'get /steel'");

    if (!Authorization::checkIsAdmin($request)) {
        return Authorization::forbidden($response);
    }

    $steels = Steel::all();

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($steels));
});
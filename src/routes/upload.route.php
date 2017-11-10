<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 11-02-17
 * Time: 22:48
 */

use Arthurius\model\Uploader;
use Arthurius\model\Authorization;

$app->post('/upload', function ($request, $response, $args) {
    $this->logger->info("Slim-Skeleton 'post /upload'");

    if (!Authorization::checkIsAdmin($request)) {
        return Authorization::forbidden($response);
    }

    $ok = Uploader::upload($request);

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


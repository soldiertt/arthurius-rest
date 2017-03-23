<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 11-02-17
 * Time: 22:48
 */

use Arthurius\model\Slider;

$app->get('/slider', function ($request, $response, $args) {
    $this->logger->info("Slim-Skeleton 'get /slider '");

    $slides = Slider::all();

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($slides));
});
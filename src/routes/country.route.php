<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 11-02-17
 * Time: 22:48
 */

use Arthurius\model\Country;

$app->get('/country', function ($request, $response, $args) {
    $this->logger->info("Slim-Skeleton 'get /country '");

    $countries = Country::all();

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($countries));
});
<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 11-02-17
 * Time: 22:48
 */

use Arthurius\model\Slider;
use Arthurius\model\Authorization;

$app->get('/slider', function ($request, $response, $args) {
    $this->logger->info("Slim-Skeleton 'get /slider '");

    $slides = Slider::all();

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($slides));
});

$app->post('/slider', function ($request, $response, $args) {

    if (!Authorization::checkIsAdmin($request)) {
        return Authorization::forbidden($response);
    }

    $slide = $request->getParsedBody();

    $this->logger->info("Slim-Skeleton 'post /slider/");
    $id = Slider::create($slide);

    $slide['id'] = $id;

    return $response->withStatus(201)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($slide));
});

$app->put('/slider/{id}', function ($request, $response, $args) {

    if (!Authorization::checkIsAdmin($request)) {
        return Authorization::forbidden($response);
    }

    $id = $request->getAttribute('id');
    $slide = $request->getParsedBody();

    $this->logger->info("Slim-Skeleton 'put /slider/".$id);
    $ok = Slider::update($id, $slide);

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($ok));
});

$app->delete('/slider/{id}', function ($request, $response, $args) {

    if (!Authorization::checkIsAdmin($request)) {
        return Authorization::forbidden($response);
    }

    $id = $request->getAttribute('id');

    $this->logger->info("Slim-Skeleton 'delete /slider/".$id);
    $ok = Slider::delete($id);

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($ok));
});
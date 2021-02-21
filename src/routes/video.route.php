<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 11-02-17
 * Time: 22:48
 */

use Arthurius\model\Video;
use Arthurius\model\Authorization;

$app->get('/videos', function ($request, $response, $args) {
    $this->logger->info("Slim-Skeleton 'get /videos '");

    $videos = Video::all();

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($videos));
});

$app->post('/videos', function ($request, $response, $args) {

    if (!Authorization::checkIsAdmin($request)) {
        return Authorization::forbidden($response);
    }

    $video = $request->getParsedBody();

    $this->logger->info("Slim-Skeleton 'post /videos/");
    $id = Video::create($video);

    $video['id'] = $id;

    return $response->withStatus(201)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($video));
});

$app->delete('/videos/{id}', function ($request, $response, $args) {

    if (!Authorization::checkIsAdmin($request)) {
        return Authorization::forbidden($response);
    }

    $id = $request->getAttribute('id');

    $this->logger->info("Slim-Skeleton 'delete /videos/".$id);
    $ok = Video::delete($id);

    return $response->withStatus(200)
        ->withHeader('Content-Type', 'application/json')
        ->write(json_encode($ok));
});

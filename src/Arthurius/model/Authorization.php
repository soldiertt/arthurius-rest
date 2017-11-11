<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 02-11-17
 * Time: 14:51
 */

namespace Arthurius\model;
use Arthurius\secrets\Secrets;

class Authorization
{
    public static function checkIsAdmin($request) {
        $headerUserId = $request->getHeader('X-Arth-UserId')[0];
        if ($headerUserId != null) {
            return in_array($headerUserId, Secrets::$ADMIN_USER_KEYS);
        }
        return false;
    }

    public static function forbidden($response) {
        $error = array('message' => 'Access denied');
        return $response->withStatus(401)
            ->withHeader('Content-Type', 'application/json')
            ->write(json_encode($error));
    }
}
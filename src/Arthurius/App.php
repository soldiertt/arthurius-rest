<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 05-10-16
 * Time: 15:59
 */

namespace Arthurius;


class App
{

    private static $DB_HOST = 'mysql';
    private static $DB_NAME = 'arthurius';
    private static $DB_USER = 'root';
    private static $DB_PASS = 'fizzye';

    private static $database;

    public static function getDb() {
        $env = getenv('ENV')?:'development';
        if ($env == 'test') {
            self::$DB_HOST = 'vps313396.ovh.net';
            self::$DB_USER = 'arthurius';
            self::$DB_PASS = 'arthurius';
        }
        if (self::$database === null) {
            self::$database = new Database(self::$DB_HOST, self::$DB_NAME, self::$DB_USER, self::$DB_PASS);
        }
        return self::$database;
    }

}
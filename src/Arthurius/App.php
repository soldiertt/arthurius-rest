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

    const DB_HOST = 'mysql';
    const DB_NAME = 'arthurius';
    const DB_USER = 'root';
    const DB_PASS = 'fizzye';

    private static $database;

    public static function getDb() {
        if (self::$database === null) {
            self::$database = new Database(self::DB_HOST, self::DB_NAME, self::DB_USER, self::DB_PASS);
        }
        return self::$database;
    }

}
<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 05-10-16
 * Time: 15:59
 */

namespace Arthurius;

use Arthurius\secrets\Secrets;

class App {
    private static $DB_LOCAL_HOST;
    private static $DB_LOCAL_NAME;
    private static $DB_LOCAL_USER;
    private static $DB_LOCAL_PASS;
    private static $DB_TEST_HOST;
    private static $DB_TEST_NAME;
    private static $DB_TEST_USER;
    private static $DB_TEST_PASS;
    private static $DB_PROD_HOST;
    private static $DB_PROD_NAME;
    private static $DB_PROD_USER;
    private static $DB_PROD_PASS;
    private static $DB_HOST;
    private static $DB_NAME;
    private static $DB_USER;
    private static $DB_PASS;

    private static $database;
    private static $env;

    static function init() {
        self::$DB_LOCAL_HOST = 'mysql';
        self::$DB_LOCAL_NAME = 'arthurius';
        self::$DB_LOCAL_USER = Secrets::$LOCAL_DB_USER;
        self::$DB_LOCAL_PASS = Secrets::$LOCAL_DB_PASS;
        self::$DB_TEST_HOST = 'vps313396.ovh.net';
        self::$DB_TEST_NAME = 'arthurius';
        self::$DB_TEST_USER = Secrets::$TEST_DB_USER;
        self::$DB_TEST_PASS = Secrets::$TEST_DB_PASS;
        self::$DB_PROD_HOST = 'arthuriuarthuriu.mysql.db';
        self::$DB_PROD_NAME = 'arthuriuarthuriu';
        self::$DB_PROD_USER = Secrets::$PROD_DB_USER;
        self::$DB_PROD_PASS = Secrets::$PROD_DB_PASS;
    }

    public static function getDb() {
        if (self::$database === null) {

            self::$env = getenv('ENVIRONMENT') ?: 'development';

            if (self::$env === 'production') {
                self::$DB_HOST = self::$DB_PROD_HOST;
                self::$DB_NAME = self::$DB_PROD_NAME;
                self::$DB_USER = self::$DB_PROD_USER;
                self::$DB_PASS = self::$DB_PROD_PASS;
            } else if (self::$env === 'test') {
                self::$DB_HOST = self::$DB_TEST_HOST;
                self::$DB_NAME = self::$DB_TEST_NAME;
                self::$DB_USER = self::$DB_TEST_USER;
                self::$DB_PASS = self::$DB_TEST_PASS;
            } else {
                self::$DB_HOST = self::$DB_LOCAL_HOST;
                self::$DB_NAME = self::$DB_LOCAL_NAME;
                self::$DB_USER = self::$DB_LOCAL_USER;
                self::$DB_PASS = self::$DB_LOCAL_PASS;
            }

            self::$database = new Database(self::$DB_HOST, self::$DB_NAME, self::$DB_USER, self::$DB_PASS);
        }
        return self::$database;
    }

}
App::init();
<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 05-10-16
 * Time: 16:25
 */

namespace Arthurius\model;

use \Arthurius\App;

class Entity
{
    protected static $table;

    public static function all() {
        return self::queryList("SELECT * FROM ".static::$table);
    }

    public static function find($id) {
        return self::queryOne("SELECT * FROM ".static::$table." WHERE id= ?", [$id]);
    }

    public static function queryOne($statement, $attributes = null) {
        return App::getDb()->queryOne($statement, get_called_class(), $attributes);
    }

    public static function queryList($statement, $attributes = null) {
        return App::getDb()->queryList($statement, get_called_class(), $attributes);
    }

    public static function insert($statement, $attributes) {
        return App::getDb()->insert($statement, get_called_class(), $attributes);
    }
}
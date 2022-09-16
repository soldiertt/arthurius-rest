<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 09-10-16
 * Time: 21:40
 */
namespace Arthurius\model;

 class Category extends Entity {

     public static $table = "section";

     public static $SQL_CREATE = <<<'EOD'
        INSERT INTO section (type, title, parent, titlenl, sortby)
        VALUES (?, ?, ?, ?, ?)
EOD;

     public static $SQL_UPDATE = <<<'EOD'
        UPDATE section SET type = ?, title = ?, parent = ?, titlenl = ?, sortby = ?
        WHERE id = ?
EOD;

     public static function allRoots() {
         return self::queryList("SELECT * FROM ".static::$table." WHERE parent is null ORDER BY sortby");
     }

     public static function subCategories($category) {
         return self::queryList("SELECT * FROM ".static::$table." WHERE parent = ? ORDER BY sortby", [$category]);
     }

     public static function findByName($category) {
         return self::queryOne("SELECT * FROM ".static::$table." WHERE type = ?", [$category]);
     }

     public static function create($category) {
         return self::insertOrUpdate(self::$SQL_CREATE,
             [$category['type'], $category['title'], $category['parent'], $category['titlenl'], $category['sortby']], true);
     }

     public static function update($id, $category) {
         return self::insertOrUpdate(self::$SQL_UPDATE,
             [$category['type'], $category['title'], $category['parent'], $category['titlenl'], $category['sortby'], $id]);
     }
 }
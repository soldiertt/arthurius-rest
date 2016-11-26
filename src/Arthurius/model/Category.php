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

     public static function allRoots() {
         return self::queryList("SELECT * FROM ".static::$table." WHERE parent = '' ORDER BY sortby");
     }

     public static function subCategories($category) {
         return self::queryList("SELECT * FROM ".static::$table." WHERE parent = ? ORDER BY sortby", [$category]);
     }

     public static function findByName($category) {
         return self::queryOne("SELECT * FROM ".static::$table." WHERE type = ?", [$category]);
     }
 }
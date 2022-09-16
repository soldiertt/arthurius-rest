<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 09-10-16
 * Time: 21:40
 */
namespace Arthurius\model;

 class Brand extends Entity {

     public static $table = "brands";

     public static function create($brand) {
         return self::insertOrUpdate("INSERT INTO ".static::$table." (brand) VALUES (?)", [$brand['brand']], true);
     }

 }
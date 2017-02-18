<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 09-10-16
 * Time: 21:40
 */
namespace Arthurius\model;

 class Order extends Entity {

     public static $table = "user_order";

     public static function allOrdersByUser($userId) {
         return self::queryList("SELECT * FROM ".static::$table." WHERE user_id = ? ORDER BY order_date desc", [$userId]);
     }

     public static function createOrder($order) {
         return self::insert("INSERT INTO ".static::$table." (user_id, order_date, order_json) VALUES (?, ?, ?)", [$order['userId'], $order['orderDate'], $order['json']]);
     }

 }
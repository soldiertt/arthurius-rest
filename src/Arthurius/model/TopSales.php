<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 09-10-16
 * Time: 21:40
 */
namespace Arthurius\model;

 class TopSales extends Entity {

     public static $table = "top_sales";

     public static $SQL_TOP_SALES_BY_PRODUCT = <<<'EOD'
        SELECT product_id, sales
        FROM top_sales
        WHERE product_id=?
EOD;

     public static function findTopSalesByProduct($productId) {
         return self::queryOne(self::$SQL_TOP_SALES_BY_PRODUCT, [$productId]);
     }

     public static function updateTopSales($orders) {
         $ok = false;
         foreach($orders as $order) {
             $count = $order['count'];
             $articleId = $order['article']['id'];
             $ok = self::insertOrUpdate("UPDATE product SET instock = 0 WHERE id= ?", [$articleId]);
             $topSale = self::findTopSalesByProduct($articleId);
             if ($topSale != null) {
                 $actualCount = $topSale->sales;
                 $actualCount += $count;
                 $ok = $ok && self::insertOrUpdate("UPDATE top_sales SET sales = ? WHERE product_id= ?", [$actualCount, $articleId]);
             } else {
                 $ok = $ok && self::insertOrUpdate("INSERT INTO top_sales (product_id, sales) VALUES (?, ?)", [$articleId, $count]);
             }
         }
         return $ok;
     }

 }
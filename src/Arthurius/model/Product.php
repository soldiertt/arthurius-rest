<?php

/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 05-10-16
 * Time: 15:38
 */

namespace Arthurius\model;

class Product extends Entity
{

    public static $table = "product";

    public static $SQL_FIND_BY_CATEGORY = <<<'EOD'
        SELECT id, ref, type, marque, name, description, picture, manche, acier, size, youtube_ref, promo, price, comment
        FROM product WHERE type=?
        ORDER BY name desc
EOD;

    public static $SQL_FIND_BY_BRAND = <<<'EOD'
        SELECT id, ref, type, marque, name, description, picture, manche, acier, size, youtube_ref, promo, price, comment
        FROM product WHERE marque=?
        ORDER BY name desc
EOD;

    public static $SQL_SEARCH = <<<'EOD'
        SELECT id, ref, type, marque, name, description, picture, manche, acier, size, youtube_ref, promo, price, comment
        FROM product
        WHERE marque like ? OR name like ? OR description like ? or manche like ?
EOD;

    public static $SQL_PROMO = <<<'EOD'
        SELECT id, ref, type, marque, name, description, picture, manche, acier, size, youtube_ref, promo, price, comment
        FROM product
        WHERE promo = true
EOD;

    public static $SQL_ALL_BRANDS = <<<'EOD'
        SELECT distinct (marque)
        FROM product
EOD;

    public static $SQL_TOP_SALES_LEAF_CATEGORY = <<<'EOD'
        SELECT id, ref, type, marque, name, description, picture, manche, acier, size, youtube_ref, promo, price, comment
        FROM product
        JOIN top_sales ON product.id = top_sales.product_id
        WHERE type=?
        ORDER BY top_sales.sales DESC
EOD;

    public static $SQL_TOP_SALES_ROOT_CATEGORY = <<<'EOD'
        SELECT id, ref, type, marque, name, description, picture, manche, acier, size, youtube_ref, promo, price, comment
        FROM product
        JOIN top_sales ON product.id = top_sales.product_id
        WHERE type IN (SELECT type FROM section WHERE parent=?)
        ORDER BY top_sales.sales DESC
EOD;

    public static function findByCategory($category) {
        return self::queryList(self::$SQL_FIND_BY_CATEGORY, [$category]);
    }

    public static function findByBrand($brandName) {
        return self::queryList(self::$SQL_FIND_BY_BRAND, [$brandName]);
    }

    public static function search($term) {
        $term = "%$term%";
        return self::queryList(self::$SQL_SEARCH, [$term, $term, $term, $term]);
    }

    public static function findPromo() {
        return self::queryList(self::$SQL_PROMO);
    }

    public static function findAllBrands() {
        return self::queryList(self::$SQL_ALL_BRANDS);
    }

    public static function findTopSalesByLeafCategory($category) {
        return self::queryList(self::$SQL_TOP_SALES_LEAF_CATEGORY, [$category]);
    }

    public static function findTopSalesByRootCategory($category) {
        return self::queryList(self::$SQL_TOP_SALES_ROOT_CATEGORY, [$category]);
    }

    public static function debug_to_console($data) {
        if (is_array($data) || is_object($data)) {
            echo("<script>console.log('PHP: " . json_encode($data) . "');</script>");
        } else {
            echo("<script>console.log('PHP: " . $data . "');</script>");
        }
    }
}
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
        SELECT id, ref, type, marque, name, description, picture, manche, acier, size, youtube_ref, promo, price, old_price, instock, comment
        FROM product WHERE type=?
        ORDER BY name desc
EOD;

    public static $SQL_FIND_BY_BRAND = <<<'EOD'
        SELECT id, ref, type, marque, name, description, picture, manche, acier, size, youtube_ref, promo, price, old_price, instock, comment
        FROM product WHERE marque=?
        ORDER BY name desc
EOD;

    public static $SQL_SEARCH = <<<'EOD'
        SELECT id, ref, type, marque, name, description, picture, manche, acier, size, youtube_ref, promo, price, old_price, instock, comment
        FROM product
        WHERE marque like ? OR name like ? OR description like ? or manche like ?
EOD;

    public static $SQL_PROMO = <<<'EOD'
        SELECT id, ref, type, marque, name, description, picture, manche, acier, size, youtube_ref, promo, price, old_price, instock, comment
        FROM product
        WHERE promo = true
EOD;

    public static $SQL_ALL_BRANDS = <<<'EOD'
        SELECT distinct (marque)
        FROM product
EOD;

    public static $SQL_TOP_SALES_LEAF_CATEGORY = <<<'EOD'
        SELECT id, ref, type, marque, name, description, picture, manche, acier, size, youtube_ref, promo, price, old_price, instock, comment
        FROM product
        JOIN top_sales ON product.id = top_sales.product_id
        WHERE type=?
        ORDER BY top_sales.sales DESC
EOD;

    public static $SQL_TOP_SALES_ROOT_CATEGORY = <<<'EOD'
        SELECT id, ref, type, marque, name, description, picture, manche, acier, size, youtube_ref, promo, price, old_price, instock, comment
        FROM product
        JOIN top_sales ON product.id = top_sales.product_id
        WHERE type IN (SELECT type FROM section WHERE parent=?)
        ORDER BY top_sales.sales DESC
EOD;

    public static function find($id) {
        $product = parent::find($id);
        return self::mapProduct($product);
    }

    public static function findByCategory($category) {
        $products = self::queryList(self::$SQL_FIND_BY_CATEGORY, [$category]);
        return self::mapProductArray($products);
    }

    public static function findByBrand($brandName) {
        $products = self::queryList(self::$SQL_FIND_BY_BRAND, [$brandName]);
        return self::mapProductArray($products);
    }

    public static function search($term) {
        $term = "%$term%";
        $products = self::queryList(self::$SQL_SEARCH, [$term, $term, $term, $term]);
        return self::mapProductArray($products);
    }

    public static function findPromo() {
        $products = self::queryList(self::$SQL_PROMO);
        return self::mapProductArray($products);
    }

    public static function findAllBrands() {
        return self::queryList(self::$SQL_ALL_BRANDS);
    }

    public static function findTopSalesByLeafCategory($category) {
        $products = self::queryList(self::$SQL_TOP_SALES_LEAF_CATEGORY, [$category]);
        return self::mapProductArray($products);
    }

    public static function findTopSalesByRootCategory($category) {
        $products = self::queryList(self::$SQL_TOP_SALES_ROOT_CATEGORY, [$category]);
        return self::mapProductArray($products);
    }

    public static function debug_to_console($data) {
        if (is_array($data) || is_object($data)) {
            echo("<script>console.log('PHP: " . json_encode($data) . "');</script>");
        } else {
            echo("<script>console.log('PHP: " . $data . "');</script>");
        }
    }

    public static function mapProduct($product) {
        $product->promo = (bool)$product->promo;
        $product->instock = (bool)$product->instock;
        return $product;
    }

    public static function mapProductArray($products) {
        return array_map("self::mapProduct", $products);
    }
}
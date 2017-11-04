<?php

/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 05-10-16
 * Time: 15:38
 */

namespace Arthurius\model;

class SlideProduct extends Entity
{

    public static $table = "slider_product";

    public static $SQL_CREATE = <<<'EOD'
        INSERT INTO slider_product (product_id) VALUES (?)
EOD;

    public static $SQL_SLIDER = <<<'EOD'
        SELECT id, type, marque, name, description, picture, manche, acier, size, youtube_ref, promo, price, old_price, instock, comment
        FROM product p, slider_product sp
        WHERE p.id = sp.product_id
EOD;

    public static function findAll() {
        $products = self::queryList(self::$SQL_SLIDER);
        return self::mapProductArray($products);
    }

    public static function create($product) {
        return self::insertOrUpdate(self::$SQL_CREATE,
            [ $product['id'] ], true);
    }

    public static function delete($id) {
        return self::insertOrUpdate("DELETE FROM ".static::$table." WHERE product_id= ?", [$id]);
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
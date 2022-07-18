<?php

namespace Arthurius\model;

class AdvancedProduct extends Entity
{

    public static $table = "product_advanced";

    public static $SQL_CREATE = <<<'EOD'
        INSERT INTO product (type, marque, name, description, picture, manche, acier, size, youtube_ref, 
          price, comment, promo, old_price, instock)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
EOD;

    public static $SQL_UPDATE = <<<'EOD'
        UPDATE product SET type= ?, marque = ?, name = ?, description = ?, picture = ?, manche = ?, 
          acier = ?, size = ?, youtube_ref = ?, price = ?, comment = ?, promo = ?, old_price = ?, instock = ? WHERE id = ?
EOD;

    public static function findAll() {
        $products = self::queryList("SELECT * FROM ".static::$table." ORDER BY brand, name");
        return self::mapAdvancedProductArray($products);
    }

    public static function mapAdvancedProduct($product) {
        $product->instock = (bool)$product->instock;
        return $product;
    }

    public static function mapAdvancedProductArray($products) {
        return array_map("self::mapAdvancedProduct", $products);
    }

}
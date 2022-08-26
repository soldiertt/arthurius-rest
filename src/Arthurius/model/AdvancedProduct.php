<?php

namespace Arthurius\model;

class AdvancedProduct extends Entity
{

    public static $table = "product_advanced";

    public static $SQL_CREATE = <<<'EOD'
        INSERT INTO product_advanced (type, name, description, pictures, youtube_ref, instock, price, comment)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
EOD;

    public static $SQL_UPDATE = <<<'EOD'
        UPDATE product_advanced SET type= ?, name = ?, description = ?, pictures = ?, youtube_ref = ?, 
        instock = ?, price = ?, comment = ? WHERE id = ?
EOD;

    public static function findAll() {
        $products = self::queryList("SELECT * FROM ".static::$table." ORDER BY type, name");
        return self::mapAdvancedProductArray($products);
    }

    public static function create($product) {
        return self::insertOrUpdate(self::$SQL_CREATE,
            [
                $product['type'], $product['name'], $product['description'], join(",", $product['pictures']),
                $product['youtube_ref'], self::toMysqlInt($product['instock']), $product['price'], $product['comment']
            ], true);
    }

    public static function update($id, $product) {
        return self::insertOrUpdate(self::$SQL_UPDATE,
            [
                $product['type'], $product['name'], $product['description'], join(",", $product['pictures']),
                $product['youtube_ref'], self::toMysqlInt($product['instock']), $product['price'], $product['comment'], $id
            ]);
    }

    public static function mapAdvancedProduct($product) {
        $product->instock = (bool)$product->instock;
        $product->pictures = array_map('trim', explode(',', $product->pictures));
        return $product;
    }

    public static function mapAdvancedProductArray($products) {
        return array_map("self::mapAdvancedProduct", $products);
    }

}
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
        SELECT id, ref, type, marque, name, description, picture, manche, acier, size, promo, price, piece
        FROM product WHERE type=?
        ORDER BY name desc
EOD;

    public static $SQL_SEARCH = <<<'EOD'
        SELECT id, ref, type, marque, name, description, picture, manche, acier, size, promo, price, piece
        FROM product
        WHERE marque like ? OR name like ? OR description like ? or manche like ?
EOD;

    public static $SQL_PROMO = <<<'EOD'
        SELECT id, ref, type, marque, name, description, picture, manche, acier, size, promo, price, piece
        FROM product
        WHERE promo = 'promo'
EOD;

    public static function findByCategory($category) {
        return self::queryList(self::$SQL_FIND_BY_CATEGORY, [$category]);
    }

    public static function search($term) {
        $term = "%$term%";
        return self::queryList(self::$SQL_SEARCH, [$term, $term, $term, $term]);
    }

    public static function findPromo() {
        return self::queryList(self::$SQL_PROMO);
    }

    public static function debug_to_console($data) {
        if (is_array($data) || is_object($data)) {
            echo("<script>console.log('PHP: " . json_encode($data) . "');</script>");
        } else {
            echo("<script>console.log('PHP: " . $data . "');</script>");
        }
    }
}
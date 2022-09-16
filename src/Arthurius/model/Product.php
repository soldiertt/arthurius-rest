<?php

namespace Arthurius\model;

class Product extends Entity
{

    public static $table = "product";

    public static $SQL_FIND_BY_CATEGORY = <<<'EOD'
        SELECT id, type, brand, name, description, picture, handle, steel, size, youtube_ref, promo, price, old_price, instock, comment
        FROM product WHERE type=?
        ORDER BY name desc
EOD;

    public static $SQL_FIND_BY_BRAND = <<<'EOD'
        SELECT id, type, brand, name, description, picture, handle, steel, size, youtube_ref, promo, price, old_price, instock, comment
        FROM product WHERE brand=?
        ORDER BY name desc
EOD;

    public static $SQL_SEARCH = <<<'EOD'
        SELECT id, type, brand, name, description, picture, handle, steel, size, youtube_ref, promo, price, old_price, instock, comment
        FROM product
        WHERE brand like ? OR name like ? OR description like ? or handle like ?
EOD;

    public static $SQL_PROMO = <<<'EOD'
        SELECT id, type, brand, name, description, picture, handle, steel, size, youtube_ref, promo, price, old_price, instock, comment
        FROM product
        WHERE promo = true
EOD;

    public static $SQL_CREATE = <<<'EOD'
        INSERT INTO product (type, brand, name, description, picture, handle, steel, size, youtube_ref, 
          price, comment, promo, old_price, instock)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
EOD;

    public static $SQL_UPDATE = <<<'EOD'
        UPDATE product SET type= ?, brand = ?, name = ?, description = ?, picture = ?, handle = ?, 
          steel = ?, size = ?, youtube_ref = ?, price = ?, comment = ?, promo = ?, old_price = ?, instock = ? WHERE id = ?
EOD;

    public static $SQL_ALL_BRANDS = <<<'EOD'
        SELECT distinct (brand)
        FROM product ORDER BY LOWER(brand)
EOD;

    public static $SQL_TOP_SALES_BY_CATEGORY = <<<'EOD'
        SELECT id, type, brand, name, description, picture, handle, steel, size, youtube_ref, promo, price, old_price, instock, comment
        FROM product
        JOIN top_sales ON product.id = top_sales.product_id
        WHERE type = ? OR type IN (SELECT type FROM section WHERE parent=?)
        ORDER BY top_sales.sales DESC
EOD;

    public static function findAll() {
        $products = self::queryList("SELECT * FROM ".static::$table." ORDER BY brand");
        return self::mapProductArray($products);
    }

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
        $brands = self::queryList(self::$SQL_ALL_BRANDS);
        return self::mapBrandArray($brands);
    }

    public static function findTopSalesByCategory($category) {
        $products = self::queryList(self::$SQL_TOP_SALES_BY_CATEGORY, [$category, $category]);
        return self::mapProductArray($products);
    }

    public static function exportProducts($category, $brand, $steel, $promo, $instock) {
        $where = "WHERE 1 = 1";
        $params = array();

        if (isset($category)) {
            $where .= " AND type = ?";
            array_push($params, $category);
        }
        if (isset($brand)) {
            $where .= " AND brand = ?";
            array_push($params, $brand);
        }
        if (isset($steel)) {
            $where .= " AND steel = ?";
            array_push($params, $steel);
        }
        if (isset($promo)) {
            $where .= " AND promo = ?";
            array_push($params, self::toMysqlInt($promo));
        }
        if (isset($instock)) {
            $where .= " AND instock = ?";
            array_push($params, self::toMysqlInt($instock));
        }

        $SQL = "SELECT id, type, brand, name, picture, handle, steel, size, youtube_ref, "
            ."promo, price, old_price, instock, comment "
            ."FROM product $where ORDER BY brand, name";

        return self::exec($SQL, $params);

    }

    public static function create($product) {
        return self::insertOrUpdate(self::$SQL_CREATE,
            [
                $product['type'], $product['brand'], $product['name'],
                $product['description'], $product['picture'], $product['handle'], $product['steel'], $product['size'],
                $product['youtube_ref'], $product['price'], $product['comment'], self::toMysqlInt($product['promo']),
                $product['old_price'], self::toMysqlInt($product['instock'])
            ], true);
    }

    public static function update($id, $product) {
        return self::insertOrUpdate(self::$SQL_UPDATE,
            [
                $product['type'], $product['brand'], $product['name'],
                $product['description'], $product['picture'], $product['handle'], $product['steel'], $product['size'],
                $product['youtube_ref'], $product['price'], $product['comment'], self::toMysqlInt($product['promo']),
                $product['old_price'], self::toMysqlInt($product['instock']), $id
            ]);
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

    public static function mapBrand($brand) {
        return array(
            'id' => base64_encode($brand->brand),
            'brand' => $brand->brand
        );
    }

    public static function mapBrandArray($brands) {
        return array_map("self::mapBrand", $brands);
    }
}
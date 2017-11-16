<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 09-10-16
 * Time: 21:40
 */
namespace Arthurius\model;

use \PDO;

class Exporter {

     public static function export($category, $brand, $steel, $promo, $instock) {

        $stmt = Product::exportProducts($category, $brand, $steel, $promo, $instock);

        return self::makeCSV($stmt);
     }

    public static function makeCSV($req){
        $csv_terminated = "\n";
        $csv_separator = ";";

        foreach(range(0, $req->columnCount() - 1) as $column_index){
            $nameCol[] = $req->getColumnMeta($column_index)['name'];
        }

        $out = implode($csv_separator, $nameCol).$csv_terminated;

        while ($row = $req->fetch(PDO::FETCH_ASSOC)) {
            $out .= implode($csv_separator, $row).$csv_terminated;
        }
        return $out;
    }

 }
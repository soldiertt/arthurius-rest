<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 09-10-16
 * Time: 21:40
 */
namespace Arthurius\model;

 class Steel extends Entity {

     public static $SQL_ALL_STEELS = <<<'EOD'
        SELECT distinct (acier)
        FROM product WHERE acier is not null ORDER BY acier
EOD;

     public static function all() {
         $steels = self::queryList(self::$SQL_ALL_STEELS);
         return self::mapSteelArray($steels);
     }

     public static function mapSteel($steel) {
         return array(
             'id' => base64_encode($steel->acier),
             'name' => $steel->acier
         );
     }

     public static function mapSteelArray($steels) {
         return array_map("self::mapSteel", $steels);
     }
 }
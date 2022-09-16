<?php
namespace Arthurius\model;

 class Steel extends Entity {

     public static $SQL_ALL_STEELS = <<<'EOD'
        SELECT distinct (steel)
        FROM product WHERE steel is not null ORDER BY steel
EOD;

     public static function all() {
         $steels = self::queryList(self::$SQL_ALL_STEELS);
         return self::mapSteelArray($steels);
     }

     public static function mapSteel($steel) {
         return array(
             'id' => base64_encode($steel->steel),
             'name' => $steel->steel
         );
     }

     public static function mapSteelArray($steels) {
         return array_map("self::mapSteel", $steels);
     }
 }
<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 09-10-16
 * Time: 21:40
 */
namespace Arthurius\model;

 class Slider extends Entity {

     public static $table = "slider";

     public static $SQL_CREATE = <<<'EOD'
        INSERT INTO slider (image, title1, title2, description, link)
        VALUES (?, ?, ?, ?, ?)
EOD;

     public static $SQL_UPDATE = <<<'EOD'
        UPDATE slider SET image = ?, title1 = ?, title2 = ?, description = ?, link = ?
        WHERE id = ?
EOD;

     public static function create($slide) {
         return self::insertOrUpdate(self::$SQL_CREATE,
             [$slide['image'], $slide['title1'], $slide['title2'], $slide['description'], $slide['link']], true);
     }

     public static function update($id, $slide) {
         return self::insertOrUpdate(self::$SQL_UPDATE,
             [$slide['image'], $slide['title1'], $slide['title2'], $slide['description'], $slide['link'], $id]);
     }
 }
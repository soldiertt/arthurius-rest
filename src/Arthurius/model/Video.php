<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 09-10-16
 * Time: 21:40
 */
namespace Arthurius\model;

 class Video extends Entity {

     public static $table = "video";

     public static $SQL_CREATE = <<<'EOD'
        INSERT INTO video (ref, title)
        VALUES (?, ?)
EOD;

     public static function create($video) {
         return self::insertOrUpdate(self::$SQL_CREATE,
             [$video['ref'], $video['title']], true);
     }

 }
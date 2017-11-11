<?php
/**
 * Created by IntelliJ IDEA.
 * User: soldi
 * Date: 09-10-16
 * Time: 21:40
 */
namespace Arthurius\model;

use Slim\Http\UploadedFile;

 class Uploader {

     const PRODUCT_TYPE = 1;
     const SLIDE_TYPE = 2;

     public static function upload($request, $type) {

         switch ($type) {
             case self::PRODUCT_TYPE:
                 $directory = '../assets/photos';
                 break;
             case self::SLIDE_TYPE:
                 $directory = '../assets/images';
                 break;
             default:
                 return false;
         }

         $filename = $request->getParam('filename');

         $exploded_filename = explode(".", $filename);
         $extension = end($exploded_filename);
         $basename = basename($filename, "." . $extension);

         $uploadedFiles = $request->getUploadedFiles();

         if ($type == self::PRODUCT_TYPE) {
             $category = $request->getParam('category');
             $directory .= DIRECTORY_SEPARATOR . $category;
         }

         // handle single input with single file upload
         $uploadedFile = $uploadedFiles['picture'];

         if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
             self::moveUploadedFile($directory, $uploadedFile, $filename);
             if ($type == self::PRODUCT_TYPE) {
                 self::makeThumbnail($directory, $filename, $basename, $extension);
             }
             return true;
         } else {
             return false;
         }
     }

     private static function moveUploadedFile($directory, UploadedFile $uploadedFile, $filename) {
         if (!is_dir($directory)) {
             mkdir($directory, 0777, true);
         }
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);
     }

     private static function makeThumbnail($directory, $filename, $basename, $extension) {
         $thumb_suffix = "m";
         $filepath = "$directory/$filename";
         $thumbfilepath = "$directory/$basename$thumb_suffix.$extension";
         $thumbnail_width = 150;
         $thumbnail_height = 100;
         $arr_image_details = getimagesize($filepath); // pass id to thumb name
         $original_width = $arr_image_details[0];
         $original_height = $arr_image_details[1];
         if ($original_width > $original_height) {
             $new_width = $thumbnail_width;
             $new_height = intval($original_height * $new_width / $original_width);
         } else {
             $new_height = $thumbnail_height;
             $new_width = intval($original_width * $new_height / $original_height);
         }
         if ($arr_image_details[2] == IMAGETYPE_GIF) {
             $imgt = "ImageGIF";
             $imgcreatefrom = "ImageCreateFromGIF";
         }
         if ($arr_image_details[2] == IMAGETYPE_JPEG) {
             $imgt = "ImageJPEG";
             $imgcreatefrom = "ImageCreateFromJPEG";
         }
         if ($arr_image_details[2] == IMAGETYPE_PNG) {
             $imgt = "ImagePNG";
             $imgcreatefrom = "ImageCreateFromPNG";
         }

         if ($imgt) {
             $old_image = $imgcreatefrom($filepath);
             $new_image = imagecreatetruecolor($new_width, $new_height);
             imagecopyresampled($new_image, $old_image, 0, 0, 0, 0, $new_width, $new_height, $original_width, $original_height);
             $imgt($new_image, $thumbfilepath);
         }
     }
 }
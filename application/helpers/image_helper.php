<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function compress_image($source, $destination)
{
    $CI =& get_instance();
    $CI->load->config('image_config');

    $driver  = $CI->config->item('image_driver');
    $img_quality  = $CI->config->item('image_quality');
    $img_path = $CI->config->item('image_magick_path');

    $result = false;

    if ($driver == 'imagemagick') {
        $result = compress_image_magic($img_path, $source, $destination);
        if ($result) {
            move_original_to_backup($source);
            return $result;
        }
    }
    
    $result = compress_image_gd($img_quality, $source, $destination);

    if (!$result) {
        log_message('error', 'GD2 compression failed for file: ' . $source);
        return false;
    }

    move_original_to_backup($source);
    return $result;
}

function compress_image_gd($img_quality, $source, $destination)
{
    if (!file_exists($source)) return false;

    $maxWidth = 1080;
    $maxHeight = 1920;

    list($width, $height, $type) = getimagesize($source);

    if (!$width || !$height) return false;

    $ratio = min($maxWidth / $width, $maxHeight / $height, 1);
    $newWidth  = (int)($width * $ratio);
    $newHeight = (int)($height * $ratio);

    switch ($type) {
        case IMAGETYPE_JPEG:
            $img = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $img = imagecreatefrompng($source);
            break;
        case IMAGETYPE_WEBP:
            $img = imagecreatefromwebp($source);
            break;
        default:
            return false;
    }
    if (!$img) return false;

    $newImg = imagecreatetruecolor($newWidth, $newHeight);

    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_WEBP) {
        imagealphablending($newImg, false);
        imagesavealpha($newImg, true);
    }

    imagecopyresampled(
        $newImg, $img,
        0, 0,
        0, 0,
        $newWidth, $newHeight,
        $width, $height
    );

    $destWebp = preg_replace('/\.(jpg|jpeg|png|webp)$/i', '.webp', $destination);

    $save = imagewebp($newImg, $destWebp, $img_quality);

    imagedestroy($img);
    imagedestroy($newImg);

    if (!$save) return false;

    return file_url_from_path($destWebp);
}

function compress_image_magic($magick, $source, $destination, $maxKB = 300)
{
    if (!file_exists($source) || !is_file($source)) return false;

    $tmp_webp = $destination . "_temp.webp";
    $tmp_jpg  = $destination . "_temp.jpg";
    $targetBytes = $maxKB * 1024;

    $cmd1 = "$magick '$source' \
        -resize '92%' \
        -strip \
        -sampling-factor 4:4:4 \
        -sharpen 0x1.0 \
        -colorspace sRGB \
        -quality 90 \
        WEBP:'$tmp_webp'";

    exec($cmd1, $out1, $ret1);

    if (!file_exists($tmp_webp)) {
        log_message('error', 'ImageMagick step1 failed: ' . json_encode($out1));
        return false;
    }

    if (file_exists($tmp_webp) && filesize($tmp_webp) <= $targetBytes) {
        rename($tmp_webp, $destination);
        return file_url_from_path($destination);
    }

    $cmd2 = "$magick '$tmp_webp' \
        -strip \
        -sampling-factor 4:4:4 \
        -sharpen 0x0.8 \
        -colorspace sRGB \
        -quality 85 \
        JPEG:'$tmp_jpg'";

    exec($cmd2, $out2, $ret2);

    if (!file_exists($tmp_jpg)) {
        log_message('error', 'ImageMagick step2 failed: ' . json_encode($out2));
        @unlink($tmp_webp);
        return false;
    }

    if (file_exists($tmp_jpg) && filesize($tmp_jpg) <= $targetBytes) {
        rename($tmp_jpg, $destination);

        @unlink($tmp_webp);

        return file_url_from_path($destination);
    }

    $cmd3 = "$magick '$tmp_jpg' \
        -strip \
        -sampling-factor 4:4:4 \
        -sharpen 0x0.8 \
        -define webp:lossless=false \
        -define webp:target-size=$targetBytes \
        WEBP:'$destination'";

    exec($cmd3, $out3, $ret3);

    @unlink($tmp_webp);
    @unlink($tmp_jpg);

    if (!file_exists($destination)) {
        log_message('error', 'ImageMagick step3 failed: ' . json_encode($out3));
        return false;
    }

    return file_url_from_path($destination);
}

function file_url_from_path($path)
{
    $relative = str_replace(FCPATH, '', $path);
    $relative = ltrim(str_replace('\\', '/', $relative), '/');

    return str_replace('uploads/', '', $relative);
}

function move_original_to_backup($source)
{
    $dir = dirname($source);
    $originalDir = $dir . '/original/';

    if (!is_dir($originalDir)) {
        mkdir($originalDir, 0777, true);
    }

    $filename = basename($source);
    $backup_path = $originalDir . $filename;

    rename($source, $backup_path);

    return $backup_path;
}
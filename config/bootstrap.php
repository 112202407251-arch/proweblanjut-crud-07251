<?php
/**
 * Bootstrap aplikasi MVC: path, helper URL, session.
 */

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH . '/app');
define('PUBLIC_PATH', ROOT_PATH . '/public');
define('UPLOAD_PATH', PUBLIC_PATH . '/uploads');

require_once ROOT_PATH . '/config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * URL relatif dari folder public (front controller).
 */
function base_url(string $path = ''): string
{
    $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
    if ($path === '') {
        return $base === '' ? '/' : $base;
    }
    return $base . '/' . ltrim($path, '/');
}

/**
 * URL file statis di folder assets.
 */
function asset_url(string $path): string
{
    $projectBase = rtrim(str_replace('\\', '/', dirname(dirname($_SERVER['SCRIPT_NAME']))), '/');
    return $projectBase . '/assets/' . ltrim($path, '/');
}

/**
 * URL upload gambar barang.
 */
function upload_url(string $filename): string
{
    return base_url('uploads/' . ltrim($filename, '/'));
}

<?php
/**
 * Front Controller – pintu masuk aplikasi MVC.
 * Akses: http://localhost/proweblanjut-crud-07251/public/
 */
require_once dirname(__DIR__) . '/config/bootstrap.php';
require_once APP_PATH . '/helpers/auth.php';
require_once APP_PATH . '/controllers/BarangController.php';

$action = $_GET['action'] ?? 'index';
$controller = new BarangController($pdo);

switch ($action) {
    case 'index':
        $controller->index();
        break;
    case 'create':
        $controller->create();
        break;
    case 'edit':
        $controller->edit();
        break;
    case 'delete':
        $controller->delete();
        break;
    default:
        $controller->index();
        break;
}

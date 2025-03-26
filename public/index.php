<?php
// include response helper
require_once '../src/helpers/HtmlResponse.php';
require_once '../src/helpers/JsonResponse.php';

// include controller
require_once '../src/controllers/FormController.php';

// this is the mini application's "router"
$controller = new FormController();

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->save();
} elseif($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller->show();
}
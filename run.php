<?php



require_once "view.php"; 
require_once "model.php"; 
require_once "controller.php"; 

$view = new View();
$model = new Model();
$controller = new Controller($view,$model);

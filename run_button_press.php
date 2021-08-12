<?php


require_once "view.php"; // view is not necessary here, as we only process data and return the results to index.php or AJAX
require_once "model.php"; 
require_once "controller.php";

$model = new Model();
$view = new View();    
$controller = new Controller($view,$model); 

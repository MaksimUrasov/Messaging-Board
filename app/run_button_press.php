<?php


require_once "app//view/class_view.php"; // view is not necessary here, as we only process data and return the results to index.php or AJAX
require_once "app/model/class_model.php"; 
require_once "app/controller/class_controller.php";

$model = new Model();
// $view = new View(10);    // now controller creates View instance when necessary
$controller = new Controller($model); 

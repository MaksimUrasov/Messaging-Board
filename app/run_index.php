<?php

require_once "app/view/class_view.php"; 
require_once "app/model/class_model.php"; 
require_once "app/controller/class_controller.php"; 

$view = new View(10);  // now controller creates View instance when necessary
$model = new Model();
$controller = new Controller($view,$model);

$controller->run();


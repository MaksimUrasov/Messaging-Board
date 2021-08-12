

<?php


require_once "view.php"; 
require_once "model.php"; 
require_once "controller.php"; 

$view = new View();
$model = new Model();
$controller = new Controller($view,$model);


?>




<!-- 
// I chose JSON because it is faster and better than XML: https://www.w3schools.com/js/js_json_xml.asp


mvc pattern
JS OOP

Bonus points:
Design Patterns are employed (Singleton, Factory, etc.)
-->




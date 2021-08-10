<?php
// below code is necessary only once, to create a new table in DB, by simply running this file.

// DB was created in serveriai.lt phpMyAdmin, as long as user (!root) has no privileges to create a new table.

// Controller is not needed here(table can be created by simply running this code), but to practive MVC
// I have added a form and a button to enter the table name. Therefore controller, view and model classes created.
// as long as these MVC classes for this file are small, I leave them all in one file.


class View {

  function __construct() {
    // seems there is nothing to construct?
  }

  public function show_the_form(){
    echo 
      '<h3>On this page we can create a table in DB.</h3>
      <form action="/db_create_table.php" method="post">
      <label for="tname">New table name:</label><br>
      <input type="text" id="tname" name="tname" value="A new awesome table"><br>
      <input type="submit" value="Create">
      </form>
      <p><br>Below there is an SQLSTATE error after submitting, I have left it as is so far as long as table name saves successfully.<p>';
  }
  public function show_the_message($t){
    echo "Table $t created successfully";
  }

}

class Model {
  private $view;
  function __construct($view) {
    $this->view = $view;
  }
  

  public function save_table_name($new_tname){
    // echo $new_name;
    $clean_table_name =  preg_replace('/\s+/', '_', $new_tname);

    try {
      
      $sql = "CREATE TABLE {$clean_table_name} (
          id INT(6) UNSIGNED AUTO_INCREMENT NOT NULL PRIMARY KEY,
          name TEXT(30) COLLATE utf8_general_ci NOT NULL,
          birth_date DATE NOT NULL,
          email TEXT(50) COLLATE utf8_general_ci,
          message TEXT(500) COLLATE utf8_general_ci NOT NULL
          );";
          
      $connection_object = new Connections_to_db;
      $connection_object->db_create($sql);

      $this->view->show_the_message($clean_table_name);
      

    } catch(PDOException $e) {
      echo $sql . "<br>" . $e->getMessage();
    }

    unset($_POST); // this does not help as long as I send POST data to the same file.
    
  }

}

class Controller {
  private $view;
  private $model;
  function __construct($view,$model) {
    $this->view = $view;
    $this->model = $model;

  }
  public function show_the_form(){
    $this->view->show_the_form();
  }
  public function save_table_name($new_name){
    $this->model->save_table_name($new_name);
  }

}

require_once 'manage_db.php';

// ok, seems we have all we need, so below we run the file: 

$view = new View();
$model = new Model($view);
$controller = new Controller($view,$model);

$controller->show_the_form();

if (isset($_POST["tname"])) {  // as long as I send POST data to file inself, POST data keeps hanging :(
  echo "<br>";
  $controller->save_table_name($_POST["tname"]);
}

<?php



class Controller {

  // private $view;   do I need to declare them here? 
  // private $model;
  
  public function __construct($view, $model) {
    $this->view = $view;
    $this->model = $model;
    $this->check_who_triggered_me();
  
  }


  public function check_who_triggered_me(){ 
    
    if (array_key_exists("first_name", $_POST)) { 
      $this->model->proceed_the_data(1); // true means INDEX.php triggered me, lets process the data
    }elseif ( json_decode(file_get_contents("php://input")) ) { 
      $this->model->proceed_the_data(0); // false means AJAX triggered me, lets process the data
      //p.s. I have left data validation to Model, as it is more work on data than logic. Also that leaves controller slim.
    } else { // there is no data to process, lets load normal html:
      $this->view->load_html();

    }

  }

}
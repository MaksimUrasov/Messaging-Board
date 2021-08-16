<?php


class Get_pdo { // singletone pattern implemented
   
    private static $instance = null;
    public $pdo;
        
    private $servername = "localhost";
    private $username = "viedis_root";
    private $password = "barinme55ageb0ard";
    private $dbname = "viedis_messageboard";
    private $table_name = "Posts3";   // O: public property reiketu vengti, nes ju reiksme galima pakeisti is bet kur, 
                                            //ir perdavus kitus duomenis kuriu nereikia.
                                            // M:  o kaip tada padaryti ta lenteles pavadinima prieinama View ir Model? Per atskira metoda?
                                            // Padariau per get_table_name() metoda.
 
    private function __construct(){
        try {
            $this->pdo = new PDO("mysql:host={$this->servername};dbname={$this->dbname}", $this->username, $this->password);
            // set the PDO error mode to exception
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "Connected to DB successfully. <br>";
        } catch(PDOException $e) {
            echo "Connection to DB failed: " . $e->getMessage();
        } 
    }

    public function get_table_name(){
        return $this->table_name; // here we receve not only an object of connection, but the PDO to work with
    }



    
    public static function get_connection(){
        if(!self::$instance){
        self::$instance = new Get_pdo();
      }
      return self::$instance->pdo; // here we receve not only an object of connection, but the PDO to work with
    }

}
 


// Class Connections_to_db {

//     public function __construct(){
//         $this->pdo = Get_pdo::get_connection();
//     }


        
//     public function db_select($sql){
//         // global $pdo;
//         $result = $this->pdo->query($sql);
//         return $result;
//     }
    
//     public function db_insert($sql,$array_to_insert){
//         // global $pdo;
//         try{
//             $result = $this->pdo->prepare($sql);
//             $result->execute($array_to_insert);

//         } catch(PDOException $e) {
//             return "Request: ". $sql . "<br>" . $e->getMessage();
//         }
//     }
// }





// Duomenų bazė:	viedis_messageboard
// Serveris (host):	localhost
// Naudotojo vardas:	viedis_root
// Slaptažodis:	barinme55ageb0ard 

// naudotojas@vienasmedis.lt
// gZ5NedQeM3tguNYR

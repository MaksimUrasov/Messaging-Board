<?php


class Connect_to_db_singletone_modified {
   
    private static $instance = null;
    private $pdo;
        
    private $servername = "localhost";
    private $username = "viedis_root";
    private $password = "barinme55ageb0ard";
    private $dbname = "viedis_messageboard";
    public static $table_name = "Posts3";
 
    private function __construct(){
        try {
            $this->pdo = new PDO("mysql:host={$this->servername};dbname={$this->dbname}", $this->username, $this->password);
            // set the PDO error mode to exception
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // echo "Connected successfully. <br>";
        } catch(PDOException $e) {
            echo "Connection to DB failed: " . $e->getMessage();
        } 
    }
    
    public static function get_connection(){
        if(!self::$instance){
        self::$instance = new Connect_to_db_singletone_modified();
      }
      return self::$instance->pdo; // here we receve not only an object of connection, but the PDO to work with
    }
}
 

$pdo = Connect_to_db_singletone_modified::get_connection(); // this can not be inside of another class.


Class Connections_to_db {

    public function db_create($sql){
        global $pdo;
        $result = $pdo->exec($sql); // use exec() because no results are returned
        return $result;
    }
        
    public function db_select($sql){
        global $pdo;
        $result = $pdo->query($sql);
        return $result;
    }
    
    public function db_insert($sql,$name,$birth,$email,$message){
        global $pdo;
        try{
            $result = $pdo->prepare($sql);
            $result->execute(array($name,$birth,$email,$message));

        } catch(PDOException $e) {
            return "Request: ". $sql . "<br>" . $e->getMessage();
        }
    }
}





// Duomenų bazė:	viedis_messageboard
// Serveris (host):	localhost
// Naudotojo vardas:	viedis_root
// Slaptažodis:	barinme55ageb0ard 

// naudotojas@vienasmedis.lt
// gZ5NedQeM3tguNYR

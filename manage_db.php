<?php





// Class Connections_to_db 
// {
//     // this class establishes a connection to DB and sets the necessary table name to create and work with.
//     // have not used __construct() because that would lead to connection to DB on every page load, which is not necessary.
    
//     // first goes variables and aclass which are necessary for communication to DB

//     private static $servername = "localhost";
//     private static $username = "viedis_root";
//     private static $password = "barinme55ageb0ard";
//     private static $dbname = "viedis_messageboard";
//     public static $table_name = "Posts3";
    


//     // private static $pdo;
//     private static $pdo;  // kodel var neveikia? nes vietoje jo naudojamas private

//     private static function db_connect(){

//         if (self::$pdo) {   // if there is a connection already, use it, or establish a new one
//             // echo "connection is already established";
//             return self::$pdo;
//         } else {
//             $servername = self::$servername;
//             $username = self::$username; 
//             $password= self::$password;
//             $dbname= self::$dbname;
                        
//             try {
//                 self::$pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
//                 // set the PDO error mode to exception
//                 self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//                 // echo "Connected successfully. <br>";
//                 return self::$pdo;
//             } catch(PDOException $e) {
//                 echo "Connection to DB failed: " . $e->getMessage();
//             } 
//         }
        
//     }
    
//     public static function db_create($sql){
//         $result = self::db_connect()->exec($sql); // use exec() because no results are returned
//         return $result;
//     }

//     public static function db_select($sql){    
//         $result = self::db_connect()->query($sql);
//         return $result;
//     }
    
//     public static function db_insert($sql,$name,$birth,$email,$message){
//         try{
//             $result = self::db_connect()->prepare($sql);
//             $result->execute(array($name,$birth,$email,$message));

//         } catch(PDOException $e) {
//             return "Request: ". $sql . "<br>" . $e->getMessage();
//         }

        
//     }
//     // public static function db_delete(){
        
//     // }


//     // $pdo= null; // when shall I close the connection? it will be closed automatically?
//     public static function db_close(){
//         $pdo = self::db_connect();
//         $pdo = null;
//     }

// }






// // Singleton to connect db.
// class Connect_to_db_singletone {
//     private static $instance = null;
//     private $pdo;    

//     private $servername = "localhost";
//     private $username = "viedis_root";
//     private $password = "barinme55ageb0ard";
//     private $dbname = "viedis_messageboard";
//     public static $table_name = "Posts3";
    
     
//     // The db connection is established in the private constructor.
//     private function __construct(){
//         try {
//             $this->pdo = new PDO("mysql:host={$this->servername};dbname={$this->dbname}", $this->username, $this->password);
//             // set the PDO error mode to exception
//             $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//             // echo "Connected successfully. <br>";
//         } catch(PDOException $e) {
//             echo "Connection to DB failed: " . $e->getMessage();
//         } 
//     }
    


//     public static function getInstance()
//     {
//       if(!self::$instance)
//       {
//         self::$instance = new Connect_to_db_singletone();
//       }
     
//       return self::$instance;
//     }
    
//     // public function getConnection() //not necessary, because we use pdo in the functions below, inside an instantiated class.
//     // {
//     //   return $this->pdo;
//     // }



//     public static function db_create($sql){

//         $result = $this->pdo->exec($sql); // use exec() because no results are returned
//         return $result;
//     }

//     public static function db_select($sql){    
//         $result = self::db_connect()->query($sql);
//         return $result;
//     }
    
//     public static function db_insert($sql,$name,$birth,$email,$message){
//         try{
//             $result = self::db_connect()->prepare($sql);
//             $result->execute(array($name,$birth,$email,$message));

//         } catch(PDOException $e) {
//             return "Request: ". $sql . "<br>" . $e->getMessage();
//         }    
//     }

// }

// // to call this second one: 
// $connection_object = Connections_to_db2::getInstance();
// $pdo = $connection_object->getConnection();











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
 
 
 
// o cia kodas kuris iskviecia:
        // $pdo = Connect_to_db_singletone_modified::get_connection();
        // var_dump( $pdo);
 

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
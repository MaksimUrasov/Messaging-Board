<?php





Class Connections_to_db 
{
    // this class establishes a connection to DB and sets the necessary table name to createa and work with.
    // have not used __construct() because that would lead to connection to DB on every page load, which is not necessary.
    
    // first goes variables and aclass which are necessary for communication to DB

    private static $servername = "localhost";
    private static $username = "viedis_root";
    private static $password = "barinme55ageb0ard";
    private static $dbname = "viedis_messageboard";
    public static $table_name = "Posts3";
    


    // private static $pdo;
    private static $pdo;  // kodel var neveikia? nes vietoje jo naudojamas private

    private static function db_connect(){

        if (self::$pdo) {   // if there is a connection already, use it, or establish a new one
            // echo "connection is already established";
            return self::$pdo;
        } else {
            $servername = self::$servername;
            $username = self::$username; 
            $password= self::$password;
            $dbname= self::$dbname;
                        
            try {
                self::$pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
                // set the PDO error mode to exception
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                // echo "Connected successfully. <br>";
                return self::$pdo;
            } catch(PDOException $e) {
                echo "Connection to DB failed: " . $e->getMessage();
            } 
        }
        
    }
    
    public static function db_create($sql){
        $result = self::db_connect()->exec($sql); // use exec() because no results are returned
        return $result;
    }

    public static function db_select($sql){    
        $result = self::db_connect()->query($sql);
        return $result;
    }
    
    public static function db_insert($sql,$name,$birth,$email,$message){
        try{
            $result = self::db_connect()->prepare($sql);
            $result->execute(array($name,$birth,$email,$message));

        } catch(PDOException $e) {
            return "Request: ". $sql . "<br>" . $e->getMessage();
        }

        
    }
    // public static function db_delete(){
        
    // }


    // $pdo= null; // when shall I close the connection? it will be closed automatically?
    public static function db_close(){
        $pdo = self::db_connect();
        $pdo = null;
    }

    




}



// // Singleton to connect db.
// class ConnectDb {
//     // Hold the class instance.
//     private static $instance = null;
//     private $conn;
    
//     private $host = 'localhost';
//     private $user = 'db user-name';
//     private $pass = 'db password';
//     private $name = 'db name';
     
//     // The db connection is established in the private constructor.
//     private function __construct()
//     {
//       $this->conn = new PDO("mysql:host={$this->host};
//       dbname={$this->name}", $this->user,$this->pass,
//       array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
//     }
    
//     public static function getInstance()
//     {
//       if(!self::$instance)
//       {
//         self::$instance = new ConnectDb();
//       }
     
//       return self::$instance;
//     }
    
//     public function getConnection()
//     {
//       return $this->conn;
//     }
//   }





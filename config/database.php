<?php
require_once __DIR__ . '/env.php'; // โหลด .env
// class Database
// {
//     private $host;
//     private $db_name;
//     private $username;
//     private $password;
//     public $conn;

//     public function __construct()
//     {
//         $this->host = getenv('DB_HOST');
//         $this->db_name = getenv('DB_NAME');
//         $this->username = getenv('DB_USER');
//         $this->password = getenv('DB_PASS');
//     }


//     public function getConnection()
//     {
//         $this->conn = null;

//         try {
//             $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->username, $this->password);
//             $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//         } catch (PDOException $exception) {
//             echo "Connection error: " . $exception->getMessage();
//         }

//         return $this->conn;
//     }
// }

class Database
{
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct()
    {
        $this->host = getenv('DB_HOST');
        $this->port = getenv('DB_PORT') ?: 4000;
        $this->db_name = getenv('DB_NAME');
        $this->username = getenv('DB_USER');
        $this->password = getenv('DB_PASS');
    }

    public function getConnection()
    {
        $this->conn = null;

        try {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};sslmode=VERIFY_IDENTITY";
            $options = [
                PDO::MYSQL_ATTR_SSL_CA => __DIR__ . '/../certs/ca.pem',  // path ต้องตรงกับที่วาง
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}

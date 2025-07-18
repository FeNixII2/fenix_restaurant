<?php
require_once __DIR__ . '/env.php'; // โหลด .env







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
            $certPath = __DIR__ . '/../certs/ca.pem'; // ต้องอยู่ใน Docker image ด้วย

            if (!file_exists($certPath)) {
                throw new Exception("❌ SSL certificate not found at $certPath");
            }

            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name}";

            $options = [
                PDO::MYSQL_ATTR_SSL_CA => $certPath,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (Exception $e) {
            echo "General error: " . $e->getMessage();
        } catch (PDOException $exception) {
            echo "Connection error: " . $exception->getMessage();
        }

        return $this->conn;
    }
}

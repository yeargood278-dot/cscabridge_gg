<?php
// config/database.php
class Database {
    private $host = "127.0.0.1";
    private $db_name = "cscabridge";
    private $username = "root"; // 生产环境请使用非root权限的专用账号
    private $password = "whyylw_666666";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // 错误抛出异常
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // 默认返回关联数组
                PDO::ATTR_EMULATE_PREPARES => false, // 禁用模拟预处理，彻底防止SQL注入
            ];
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch(PDOException $exception) {
            // 生产环境应记录到日志，而不是直接输出
            error_log("Database Connection Error: " . $exception->getMessage());
            die("Database connection failed. Please try again later.");
        }
        return $this->conn;
    }
}
?>
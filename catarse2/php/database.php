<?php
// config/Database.php
class Database {
    private $host = 'localhost';
    private $db_name = 'catarse';
    private $username = 'root';
    private $password = '@Breporta125722'; // XAMPP default often uses an empty password for 'root'
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            // Primeiro tenta conectar sem especificar o banco para verificar se o MySQL está rodando
            $test_conn = new PDO("mysql:host=" . $this->host, $this->username, $this->password);
            
            // Verifica se o banco existe, se não, cria
            $stmt = $test_conn->query("SHOW DATABASES LIKE '" . $this->db_name . "'");
            if (!$stmt->fetch()) {
                error_log("Banco 'catarse' não existe, criando...");
                $test_conn->exec("CREATE DATABASE " . $this->db_name);
                error_log("Banco 'catarse' criado com sucesso");
            }
            
            // Agora conecta ao banco específico
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8", 
                $this->username, 
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                )
            );
            
            error_log("Conexão com banco 'catarse' estabelecida com sucesso");
            
        } catch(PDOException $exception) {
            error_log("Erro de conexão com banco: " . $exception->getMessage());
            throw new Exception("Erro de conexão com banco de dados: " . $exception->getMessage());
        }
        return $this->conn;
    }
}
?>
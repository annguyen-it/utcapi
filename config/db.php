<?php
    include_once dirname(__DIR__) . '/utils/env_io.php';

    class Database
    {
        private string $host;
        private string $db_name;
        private string $username;
        private string $password;

        private PDO $connect;

        public function __construct ($isMainDB)
        {
            EnvIO::loadEnv();
            if ($isMainDB) {
                $this->host     = $_ENV['DB_HOST'];
                $this->db_name  = $_ENV['DB_NAME'];
                $this->username = $_ENV['DB_USER'];
                $this->password = $_ENV['DB_PASS'];
            }
            else {
                $this->host     = $_ENV['EXTRA_DB_HOST'];
                $this->db_name  = $_ENV['EXTRA_DB_NAME'];
                $this->username = $_ENV['EXTRA_DB_USER'];
                $this->password = $_ENV['EXTRA_DB_PASS'];
            }
        }

        public function connect () : PDO
        {
            try {
                $this->connect = new PDO(
                    "mysql:charset=utf8mb4;
                    host=$this->host;
                    dbname=$this->db_name",
                    $this->username,
                    $this->password
                );
                $this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->connect->exec('set names utf8');
            } catch (PDOException $error) {
                throw $error;
            }

            return $this->connect;
        }
    }

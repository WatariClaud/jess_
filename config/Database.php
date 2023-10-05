<?php

    class Database {
        // private $host = 'localhost';
        // private $dbname = 'jess';
        // private $username = 'root';
        // private $password  = '';
        // private $connection;
        
        // private $host = 'us-cdbr-east-04.cleardb.com';
        // private $dbname = 'heroku_2b643ff81dafd74';
        // private $username = 'b9eab9715fd32d';
        // private $password  = 'cbfe69e7';
        
        private $host = 'viaduct.proxy.rlwy.net';
        private $dbname = 'railway';
        private $username = 'root';
        private $password  = 'ajv478ngba6b7si6gcbeosl5xtlv2qc$';

        public function connect() {
            $this->connection = null;

            try {
                $this->connection = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->dbname, $this->username, $this->password);

                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo 'Connection error: ' . $e->getMessage();
            }

            return $this->connection;
        }
    };

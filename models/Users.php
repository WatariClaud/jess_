<?php

    include_once '../authenticate_token.php';

    class User {
        private $connection;
        private $table = 'users';

        public $id;
        public $name;
        public $phone;
        public $password;

        public function __construct($db) {
            $this->connection = $db;
        }

        public function get() {
            $query = 'SELECT * FROM ' . $this->table . ' ORDER BY id DESC';

            $statement=$this->connection->prepare($query);

            $statement->execute();

            return $statement;
        }

        public function get_single() {

            $headers = getallheaders();

            $token = $headers['Authorization'];

            $token_auth = new token_auth();
        
            $token_auth_ = $token_auth->is_jwt_valid($token);

            $user_id = json_encode($token_auth_);

            $user_id = explode('"', $user_id)[1];

            $user_id = explode('"', $user_id)[0];

            $query = 'SELECT * FROM ' . $this->table . ' WHERE phone = ?';

            $statement=$this->connection->prepare($query);

            $statement->bindParam(1, $user_id);

            $statement->execute();

            $row = $statement->fetch(PDO::FETCH_ASSOC);

            $this->id=$row['id'];
            $this->name=$row['name'];
            $this->phone=$row['phone'];
            $this->password=$row['password'];
        }

        public function create() {
            $search_email_query = 'SELECT * FROM ' . $this->table . ' WHERE phone = ?';

            $search_email_statement=$this->connection->prepare($search_email_query);

            $search_email_statement->bindParam(1, $this->phone);

            $search_email_statement->execute();

            $row = $search_email_statement->fetch(PDO::FETCH_ASSOC);

            $phone = $row['phone'];

            if($phone) {
                echo json_encode(
                    array('Error' => 'Duplicate credentials')
                );
                return false;
            }

            $query = 'INSERT INTO ' . $this->table . ' SET name = :name, phone= :phone, password = :password';

            $statement = $this->connection->prepare($query);

            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->phone = htmlspecialchars(strip_tags($this->phone));
            $this->password = htmlspecialchars(strip_tags($this->password));

            $statement->bindParam(':name', $this->name);
            $statement->bindParam(':phone', $this->phone);
            $statement->bindParam(':password', $this->password);

            if($statement->execute()) {
                return true;
            } else {

                printf('Error: %s \n', $statement->error);

                return false;
            }
        }

        
        public function authenticate() {
            
            $query = 'SELECT * FROM ' . $this->table . ' WHERE phone = ?';

            $statement=$this->connection->prepare($query);

            $statement->bindParam(1, $this->phone);

            $statement->execute();

            $row = $statement->fetch(PDO::FETCH_ASSOC);

            if(!$row) {
                echo json_encode(
                    array('Error' => 'Invalid credentials entered')
                );
                return false;
            }

            $hashed_password = $row['password'];
            
            $is_valid_password = password_verify($this->password, $hashed_password);

            if(!$is_valid_password) {
                echo json_encode(
                    array('Error' => 'Incorrect password')
                );
                return false;
            }

            if($statement->execute()) {
                return true;
            } else {

                printf('Error: %s \n', $statement->error);

                return false;
            }
        }
    };

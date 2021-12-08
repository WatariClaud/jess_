<?php

    include_once '../authenticate_token.php';

    class Parking {
        private $connection;
        private $table = 'parking';
        private $table_next = 'users';

        public $id;
        public $space;
        public $status;
        public $spots;
        public $priceperspot;

        public function __construct($db) {
            $this->connection = $db;
        }
        public function get_available() {
            $query = 'SELECT * FROM ' . $this->table . ' WHERE spots > ? ORDER BY id DESC';

            $statement=$this->connection->prepare($query);

            $param = 0;

            $statement->bindParam(1, $param);

            $statement->execute();

            return $statement;
        }
        
        public function create() {
            $query = 'INSERT INTO ' . $this->table . ' SET space = :space, spots= :spots, priceperspot = :priceperspot';

            $statement = $this->connection->prepare($query);

            $this->space = htmlspecialchars(strip_tags($this->space));
            $this->spots = htmlspecialchars(strip_tags($this->spots));
            $this->priceperspot = $this->priceperspot;

            $statement->bindParam(':space', $this->space);
            $statement->bindParam(':spots', $this->spots);
            $statement->bindParam(':priceperspot', $this->priceperspot);

            if($statement->execute()) {
                return true;
            } else {

                printf('Error: %s \n', $statement->error);

                return false;
            }
        }
        
        public function book() {

            $token_auth = new token_auth();

            $headers = getallheaders();

            $token = $headers['Authorization'];

            $token_auth_ = $token_auth->is_jwt_valid($token);

            $user_id = json_encode($token_auth_);

            $user_id = explode('"', $user_id)[1];

            $user_id = explode('"', $user_id)[0];

            $query_get_user = 'SELECT * FROM ' . $this->table_next . ' WHERE phone = ?';

            $statement_get_user=$this->connection->prepare($query_get_user);

            $statement_get_user->bindParam(1, $user_id);

            $statement_get_user->execute();

            $row_get_user = $statement_get_user->fetch(PDO::FETCH_ASSOC);

            $user_id = $row_get_user['id'];

            if(!$user_id) {
                echo json_encode(
                    array('Error' => 'Retry your login')
                );

                return false;
            }

            $query = 'SELECT * FROM ' . $this->table . ' WHERE id = ?';

            $statement=$this->connection->prepare($query);

            $statement->bindParam(1, $this->id);

            $statement->execute();

            $row = $statement->fetch(PDO::FETCH_ASSOC);

            if(!$row['id']) {
                echo json_encode(
                    array('Error: ' => 'Invalid request.')
                );
                return false;
            }

            if($row['spots'] < 1) {
                echo json_encode(
                    array('Error' => 'No more available spaces.')
                );
                return false;
            }

            $spots = $row['spots'] -1;

            $query = 'UPDATE ' . $this->table . ' SET spots = :spots WHERE id = :id';

            $statement = $this->connection->prepare($query);

            $this->spots = $spots;
            $this->id = $this->id;

            $statement->bindParam(':spots', $this->spots);
            $statement->bindParam(':id', $this->id);

            if($statement->execute()) {
                return true;
            } else {

                printf('Error: %s \n', $statement->error);

                return false;
            }
        }
    };
<?php

    include_once '../authenticate_token.php';

    class Ticket {
        private $connection;
        private $table = 'tickets';
        private $table_next = 'users';
        private $next_table = 'parking';

        public $userid;
        public $status;
        public $date;
        public $datepaid;
        public $ispaid;
        public $amount;
        public $name;
        public $phone;
        public $password;

        public function __construct($db) {
            $this->connection = $db;
        }

        public function get_user_tickets() {

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

            $this->userid=$row_get_user['id'];
            $this->name=$row_get_user['name'];
            $this->phone=$row_get_user['phone'];
            $this->password=$row_get_user['password'];

            $query = 'SELECT * FROM ' . $this->table . ' WHERE userid = ? ORDER BY id DESC';

            $statement=$this->connection->prepare($query);

            $param = $this->userid;

            $statement->bindParam(1, $param);

            $statement->execute();

            return $statement;
        }
        
        public function create() {

            $headers = getallheaders();

            $token = $headers['Authorization'];

            $token_auth = new token_auth();
        
            $token_auth_ = $token_auth->is_jwt_valid($token);

            $user_id = json_encode($token_auth_);

            $user_id = explode('"', $user_id)[1];

            $user_id = explode('"', $user_id)[0];

            $query_get_user = 'SELECT * FROM ' . $this->table_next . ' WHERE phone = ?';

            $statement_get_user=$this->connection->prepare($query_get_user);

            $statement_get_user->bindParam(1, $user_id);

            $statement_get_user->execute();

            $row_get_user = $statement_get_user->fetch(PDO::FETCH_ASSOC);

            $this->userid=$row_get_user['id'];

            if(!$this->userid) {
                echo json_encode(
                    array('Error' => 'Retry your login')
                );

                return false;
            }

            $query_get_parking = 'SELECT * FROM ' . $this->next_table . ' WHERE id = ?';

            $statement_get_parking =$this->connection->prepare($query_get_parking);

            $statement_get_parking->bindParam(1, $this->id);

            $statement_get_parking->execute();

            $row_get_parking = $statement_get_parking->fetch(PDO::FETCH_ASSOC);

            if(!$row_get_parking['id']) {
                echo json_encode(
                    array('Error: ' => 'Invalid request.')
                );
                return false;
            }

            $query = 'INSERT INTO ' . $this->table . ' SET userid = :userid,  datecreated = :date, status= :status, ispaid = :ispaid, amount = :amount, datepaid = :datedue, spaceid = :spaceid';

            $statement = $this->connection->prepare($query);

            $status = 'active';
            $ispaid = false;

            $this->userid = $this->userid;
            $this->status = $status;
            $this->date = $this->date;
            $this->ispaid = $ispaid;
            $this->amount = $row_get_parking['priceperspot'];
            $this->datedue = $this->datedue;
            $this->spaceid = $row_get_parking['space'];

            $statement->bindParam(':userid', $this->userid);
            $statement->bindParam(':status', $this->status);
            $statement->bindParam(':date', $this->date);
            $statement->bindParam(':ispaid', $this->ispaid);
            $statement->bindParam(':amount', $this->amount);
            $statement->bindParam(':datedue', $this->datedue);
            $statement->bindParam(':spaceid', $this->spaceid);

            if($statement->execute()) {
                return true;
            } else {

                printf('Error: %s \n', $statement->error);

                return false;
            }
        }

        public function pay_ticket() {

            $headers = getallheaders();

            $token = $headers['Authorization'];

            $token_auth = new token_auth();
        
            $token_auth_ = $token_auth->is_jwt_valid($token);

            $user_id = json_encode($token_auth_);

            $user_id = explode('"', $user_id)[1];

            $user_id = explode('"', $user_id)[0];

            $query_get_ticket = 'SELECT * FROM ' . $this->table . ' WHERE id = ?';

            $statement_get_ticket =$this->connection->prepare($query_get_ticket);

            $param = $this->id;

            $statement_get_ticket ->bindParam(1, $param);

            $statement_get_ticket ->execute();

            $row = $statement_get_ticket->fetch(PDO::FETCH_ASSOC);

            if(!$row['id']) {
                echo json_encode(
                    array('Error' => 'Invalid ticket number. Could not complete your request.')
                );

                return false;
            }

            if($row['ispaid']) {
                echo json_encode(
                    array('Error' => 'Your ticket is already fulfilled. Please buy another one.')
                );

                return false;
            }

            $query = 'UPDATE ' . $this->table . ' SET status= :status, ispaid = :ispaid, datepaid = :datepaid, amount = :amount WHERE id = :id' ;

            $statement = $this->connection->prepare($query);

            $status = 'inactive';
            $ispaid = true;

            $this->status = $status;
            $this->ispaid = $ispaid;
            $this->datepaid = $this->datepaid;
            $this->amount = $this->amount;

            $statement->bindParam(':status', $this->status);
            $statement->bindParam(':ispaid', $this->ispaid);
            $statement->bindParam(':datepaid', $this->datepaid);
            $statement->bindParam(':amount', $this->amount);
            $statement->bindParam(':id', $this->id);

            if($statement->execute()) {

                $query_get_parking = 'SELECT * FROM ' . $this->next_table . ' WHERE space = ?';
    
                $statement_get_parking =$this->connection->prepare($query_get_parking);
    
                $statement_get_parking->bindParam(1, $row['spaceid']);
    
                $statement_get_parking->execute();
                
                $row_get_parking = $statement_get_parking->fetch(PDO::FETCH_ASSOC);

                $new_spots = $row_get_parking['spots'] + 1;
    
                $query_update_parking = 'UPDATE ' . $this->next_table . ' SET spots = :spots WHERE space = :space';
    
                $statement_update_parking =$this->connection->prepare($query_update_parking);

                $statement_update_parking->bindParam(':spots', $new_spots);
                $statement_update_parking->bindParam(':space', $row['spaceid']);

                if($statement_update_parking->execute()) {
                    return true;
                } else {

                    printf('Error: %s \n', $statement_update_parking->error);
    
                    return false;
                }
                return true;
            } else {

                printf('Error: %s \n', $statement->error);

                return false;
            }
        }
    };
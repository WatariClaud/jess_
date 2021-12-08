<?php

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: Application/Json');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Methods, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');


    include_once '../../config/Database.php';
    include_once '../../models/Tickets.php';

    $database = new Database();

    $db = $database->connect();

    $ticket = new Ticket($db);

    $result = $ticket->get_user_tickets();

    $num = $result->rowCount();

    if($num > 0) {
        $tickets_array = array();
        $tickets_array['data'] = array();

        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $ticket_entity = array(
                'id' => $id,
                'userid' => $userid,
                'datecreated' => $datecreated,
                'status' => $status,
                'ispaid' => $ispaid,
                'datepaid' => $datepaid,
                'amount' => $amount,
                'spaceid' => $spaceid
            );

            array_push($tickets_array['data'], $ticket_entity);
        }

        echo json_encode($tickets_array);
    } else {
        echo json_encode(
            array('Message' => 'No tickets are available')
        );   
    }
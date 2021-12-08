<?php

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: Application/Json');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Methods, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../../config/Database.php';
    include_once '../../models/Tickets.php';

    $database = new Database();

    $db = $database->connect();

    $ticket = new Ticket($db);

    $ticket->id = isset($_GET['id']) ? $_GET['id'] : die();

    $data = json_decode(file_get_contents('php://input'));

    if(!$data) {
        echo 'Data is required';

        return false;
    }

    $ticket->datepaid = $data->datepaid;
    $ticket->amount = $data->amount;

    if($ticket->pay_ticket()) {
        
        echo json_encode(
            array('Message' => 'Paid for ticket successfully.')
        );
    } else {
        echo json_encode(
            array('Message' => 'Unable to clear your ticket.')
        );
    }
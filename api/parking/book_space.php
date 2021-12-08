<?php

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: Application/Json');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Methods, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../../config/Database.php';
    include_once '../../models/Parking.php';
    include_once '../../models/Tickets.php';

    $database = new Database();

    $db = $database->connect();

    $parking = new Parking($db);

    $ticket = new Ticket($db);

    $parking->id = isset($_GET['id']) ? $_GET['id'] : die();

    $ticket->id = isset($_GET['id']) ? $_GET['id'] : die();

    $data = json_decode(file_get_contents('php://input'));

    if(!$data->date) {
        echo json_encode(
            array('Error: ' => 'Date is required.')
        );

        return false;
    }

    if(!$data->datedue) {
        echo json_encode(
            array('Error: ' => 'Date due is required.')
        );

        return false;
    }

    $ispaid = false;
    
    $ticket->date = $data->date;
    $ticket->datedue = $data->datedue;

    if($parking->book() && $ticket->create()) {
        echo json_encode(
            array('Message' => 'Submitted request successfully.')
        );
    } else {
        echo json_encode(
            array('Message' => 'Error sending request.')
        );
    }
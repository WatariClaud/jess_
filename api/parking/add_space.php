<?php

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: Application/Json');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Methods, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../../config/Database.php';
    include_once '../../models/Parking.php';

    $database = new Database();

    $db = $database->connect();

    $parking = new Parking($db);

    $data = json_decode(file_get_contents('php://input'));

    if(!$data->space) {
        echo json_encode(
            array('Message' => 'Location is required')
        );

	    return false;
    }

    if(!$data->spots) {
        echo json_encode(
            array('Message' => 'Number of spots available is required')
        );

	    return false;
    }

    if(!$data->priceperspot) {
        echo json_encode(
            array('Message' => 'Price per spot available is required')
        );

	    return false;
    }


    $parking->space = $data->space;
    $parking->spots = $data->spots;
    $parking->priceperspot = $data->priceperspot;

    if($parking->create()) {
        
        echo json_encode(
            array('Message' => 'Added parking space successfully.')
        );
    } else {
        echo json_encode(
            array('Message' => 'Error adding parking space.')
        );
    }
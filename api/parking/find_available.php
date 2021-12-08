<?php

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: Application/Json');

    include_once '../../config/Database.php';
    include_once '../../models/Parking.php';

    $database = new Database();

    $db = $database->connect();

    $parking = new Parking($db);

    $result = $parking->get_available();

    $num = $result->rowCount();

    if($num > 0) {
        $parking_array = array();
        $parking_array['data'] = array();

        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $parking_entity = array(
                'id' => $id,
                'space' => $space,
                'spots' => $spots,
                'priceperspot' => $priceperspot
            );

            array_push($parking_array['data'], $parking_entity);
        }

        echo json_encode($parking_array);
    } else {
        echo json_encode(
            array('Message' => 'No parking spots are currently available')
        );   
    }
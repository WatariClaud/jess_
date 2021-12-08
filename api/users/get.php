<?php

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: Application/Json');

    include_once '../../config/Database.php';
    include_once '../../models/Users.php';

    $database = new Database();

    $db = $database->connect();

    $user = new User($db);

    $result = $user->get();

    $num = $result->rowCount();

    if($num > 0) {
        $users_array = array();
        $users_array['data'] = array();

        while($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $user_entity = array(
                'id' => $id,
                'name' => $name,
                'phone' => $phone,
                'password' => $password
            );

            array_push($users_array['data'], $user_entity);
        }

        echo json_encode($users_array);
    } else {
        echo json_encode(
            array('Message: ' => 'No users found')
        );   
    }
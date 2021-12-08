<?php

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: Application/Json');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Methods, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');


    include_once '../../config/Database.php';
    include_once '../../models/Users.php';

    $database = new Database();

    $db = $database->connect();

    $user = new User($db);

    $user->get_single();

    if(!$user->id) {
        echo json_encode(
            array('Message: ' => 'Invalid user id')
        );

        return false;
    } else {

        $post_array = array(
            'id' => $user->id,
            'name' =>$user->name,
            'phone' => $user->phone,
            'password' => $user->password
        );
    
        print_r(json_encode($post_array));
    }
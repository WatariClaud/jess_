<?php

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: Application/Json');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Access-Control-Allow-Origin, Access-Control-Allow-Methods, Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With');

    include_once '../../config/Database.php';
    include_once '../../models/Users.php';

    $database = new Database();

    $db = $database->connect();

    $user = new User($db);

    $data = json_decode(file_get_contents('php://input'));
    
    if(!$data->name) {
        echo json_encode(
            array('Error' => 'Name is required.')
        );

        return false;
    }
    
    if(!$data->phone) {
        echo json_encode(
            array('Error' => 'Phone is required.')
        );

        return false;
    }
    
    if(!$data->password) {
        echo json_encode(
            array('Error' => 'Password is required.')
        );

        return false;
    }

    $user->name = $data->name;
    $user->phone = $data->phone;
    $user->password = password_hash($data->password, PASSWORD_DEFAULT);

    if($user->create()) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        
        $payload = json_encode(['user' => $data->phone]);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));

        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'jess-app', true);

        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;

        echo json_encode(
            array('Message' => 'Added user successfully.', 'token' => $jwt)
        );
    } else {
        echo json_encode(
            array('Message' => 'Error adding user.')
        );
    }
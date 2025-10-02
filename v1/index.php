<?php

    header('Content-Type: application/json');

    if (!isset($_GET['apiKey'])){
        $data = array('status'=> 0, 'message'=> 'No API key provided.');
        echo json_encode($data);
        return;
    }

    include_once 'connect.php';

    include_once '../library/php-jwt/jwt.php';

    use Firebase\JWT\JWT;


    $apiKey = $_GET['apiKey'];


    if ($apiKey == 'e0189e0c-0bb7-4b69-abe5-a54dcb2011e8'){
        if (!isset($_GET['action'])){
            $data = array('status'=> 0, 'message'=> 'No action requested.');
            echo json_encode($data);
            return;
        }
        $actionRequested = $_GET['action'];
        if ($actionRequested == 'postData'){
            $UID = $_GET['UID'];
            $UsernameRBX = $_GET['Username'];
            $json = file_get_contents('php://input');
            $obj = json_decode($json, true);

            //insert if usernameRBX not exist 
            $query = "SELECT * FROM data WHERE UsernameRoblocc = '$UsernameRBX'";
            $result = $db -> query($query);

            $note = isset($obj['Note']) ? $obj['Note'] : null;


            $createAt = new DateTime('now');
            $updateAt = new DateTime('now');

            $createAt = $createAt -> format('Y/m/d H:i:s');
            $updateAt = $updateAt -> format('Y/m/d H:i:s');


            if (mysqli_num_rows($result) == 0){
                $query = "INSERT INTO data VALUES (NULL,'$UID', '$UsernameRBX', '$json', '$createAt', '$updateAt', '$note')";
                $result = $db -> query($query);

                $data = array('status'=> 1, 'message'=> 'Username not exist. Inserted new data.');

            } else {
                $query = "UPDATE data SET Description = '$json', updatedAt = '$updateAt', Note = '$note' WHERE UsernameRoblocc = '$UsernameRBX' AND UID = '$UID'";
                $result = $db -> query($query);

                $data = array('status'=> 1, 'message'=> 'Username exist. Updated data.');
            }


        }
        else {
            $data = array('status'=> 0, 'message'=> 'Invalid action requested.');
        }
    } else {
        $data = array('status'=> 0, 'message'=> 'API key is invalid.');
    }

    echo json_encode($data);

?>
<?php

    header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');
    require '../../connect.php';

    require '../../../library/php-jwt/JWT.php';
    

    use Firebase\JWT\JWT;
    
    
    if (!isset($_POST['username']) && !isset($_POST['password'])){
        $data = array('status'=> 0, 'message'=> 'Missing data');
        echo $_POST['username'];
        echo $_POST['password'];
        echo json_encode($data);
        return;
    }

    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $query = "SELECT * FROM account WHERE Username = '$username' AND Password = '$password'";
    $result = $db -> query($query);

    $id = $result -> fetch_assoc()['UID'];

    if (mysqli_num_rows($result) == 1){
        $payload = [
            "UID" => $id,
            "iat" => time(),
            "exp" => time() + 60 * 60 * 24 * 7
        ];

        $jwt = JWT::encode($payload, '884ea07d-a322-4be4-956f-20f646ea85a9', 'HS256');

        $data = array('status'=> 1, 'message'=> 'Login successful.', 'token'=> $jwt);
        } else {
        $data = array('status'=> 0, 'message'=> 'Login failed.');
    }

    echo json_encode($data);

?>
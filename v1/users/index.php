<?php
    if (!isset($_GET['apiKey'])){
        $data = array('status'=> 0, 'message'=> 'No API key provided.');
        echo json_encode($data);
        return;
    }
    
    $apiKey = $_GET['apiKey'];
    
    if ($apiKey != 'e0189e0c-0bb7-4b69-abe5-a54dcb2011e8'){
        $data = array('status'=> 0, 'message'=> 'API key is invalid.');
        echo json_encode($data);
        return;
    }

    include_once '../connect.php';
    

    $json = file_get_contents('php://input');
    $obj = json_decode($json, true);
    
    if(!isset($obj)){
        $data = array('status'=> 0, 'message'=> 'Invalid Request Data.');
        echo json_encode($data);
        return;
    }
    

    $UID = $obj['UID'];

    $query = "SELECT * FROM data WHERE UID = '$UID' GROUP BY Note";

    $result = $db -> query($query);

    $data = array('status'=> 1, 'message'=> 'Data fetched.', 'data'=> json_encode($result -> fetch_assoc()));
?>
<?php
$object_name = "user";
require "../../../config.php";


function getUser($id){
    global $mysqli;
    $query = " SELECT * FROM user WHERE id = ".$id;
    if( $result = $mysqli->query($query)){
        if ( $row = $result->fetch_object()) {
            return extractUserRow($row);
        }
    }
}

function extractUserRow($row){
    $user = new stdClass();
    $user->id = $row->id;
    $user->username = $row->username;
    $user->email = $row->email;
    //$user->phone = $row->phone;
    $user->login_status = $row->login_status;
    $user->avail_status = $row->avail_status;
    //$user->is_song = $row->is_song;
    //$user->is_console = $row->is_console;
    //$user->is_buyside = $row->is_buyside;
    //$user->is_sellside = $row->is_sellside;
    //$user->is_mentor = $row->is_mentor;
    return $user;
}

$methods = array("GET","POST","PUT");
$method = $_SERVER['REQUEST_METHOD'];
if ( $method == "GET"){
    header('Content-Type: application/json');
    if ( isset($_GET["id"])){
        $user = getUser($_GET["id"]);
        $json = new stdClass();
        $json->$object_name = $user;
        echo json_encode($json,JSON_NUMERIC_CHECK);
    }
    else{
        $query = " SELECT * FROM user";
        if ( $result = $mysqli->query($query)){
            $users = array();
            while ( $row = $result->fetch_object()){
                $users[] = extractUserRow($row);
            }
            echo '{"'.$object_name."\":".json_encode($users, JSON_NUMERIC_CHECK)."}";
        }
    }
}
else if ( $method == "POST"){
    header('Content-Type: application/json');
    $json_data = json_decode(file_get_contents("php://input"),false);
    //file_put_contents('php://stderr', print_r(file_get_contents("php://input"), TRUE)); //Logging to Apache Log
    $user = $json_data->$object_name;
    $avail_status = ( isset($user->avail_status) && $user->avail_status == 1 )? 1:0;

    $sql = "UPDATE user SET avail_status = ? WHERE id = ? AND username=? ";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("iis",
            $avail_status,
            $user->id,
            $user->username
        );
        if ($stmt->execute()) {
                $json = new stdClass();
                $json->$object_name = getUser($user->id);
                echo json_encode($json,JSON_NUMERIC_CHECK);
        } else {
            header('HTTP/1.1 500 Internal Server Error');
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        $stmt->close();
    }

}
else if ( $method == "PUT"){
    header("HTTP/1.0 405 Method Not Allowed");
}
else{
    header("HTTP/1.0 405 Method Not Allowed");
}



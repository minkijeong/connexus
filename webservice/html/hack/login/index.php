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

function getUserByUserCredential($username, $password){
    global $mysqli;
    $query = " SELECT * FROM user WHERE username = \"".$username."\" AND password = MD5(\"".$password."\")";
    //echo $query;
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

if ( $method == "POST"){
    header('Content-Type: application/json');
    $json_data = json_decode(file_get_contents("php://input"),false);
    //file_put_contents('php://stderr', print_r(file_get_contents("php://input"), TRUE)); //Logging to Apache Log
    $user_input = $json_data->$object_name;
    $user_found = getUserByUserCredential($user_input->username, $user_input->password);
    //echo "USER ID: ".$user_found->id;
    if ($user_found->id){
        //Valida user selected
        $login_status = 1;

        $sql = "UPDATE user SET login_status = ? WHERE id=?";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("ii",
                $login_status,
                $user_found->id
            );
            if ($stmt->execute()) {
                $json = new stdClass();
                $json->$object_name = getUser($user_found->id);
                echo json_encode($json,JSON_NUMERIC_CHECK);
            } else {
                header('HTTP/1.1 500 Internal Server Error');
                echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            }
            $stmt->close();
        }
    }


}
else if ( $method == "PUT"){
    header("HTTP/1.0 405 Method Not Allowed");
}
else{
    header("HTTP/1.0 405 Method Not Allowed");
}
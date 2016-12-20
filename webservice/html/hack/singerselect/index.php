<?php
$object_name = "user";
require "../../../config.php";
require "../job/job_func.php";

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
    $user->phone = $row->phone;
    $user->login_status = $row->login_status;
    $user->avail_status = $row->avail_status;
    $user->is_song = $row->is_song;
    $user->is_console = $row->is_console;
    //$user->is_buyside = $row->is_buyside;
    //$user->is_sellside = $row->is_sellside;
    $user->is_mentor = $row->is_mentor;
    return $user;
}

$methods = array("GET");
$method = $_SERVER['REQUEST_METHOD'];
if ( $method == "GET"){
    header('Content-Type: application/json');
    $category = isset($_GET["cat"])? $_GET["cat"]: "0";
    switch ($category){
        case "1": //song
            $category_condition = " AND is_song = 1";
            break;
        case "2": //console
            $category_condition = " AND is_console = 1";
            break;
        case "3": //mentoring
            $category_condition = " AND is_mentor = 1";
            break;
        default: //any
            $category_condition = " ";
    }

    // Selecting available caller
    $sql = "SELECT user.id FROM user WHERE
          user.avail_status = 1 ".$category_condition." AND user.id NOT IN 
        (SELECT userid FROM job_status WHERE status IN (\"new\", \"processing\") AND userid IS NOT NULL )
        ORDER BY RAND() LIMIT 1";

    //echo "SQL : ".$sql."\n";
    if( $result = $mysqli->query($sql)){
        if ( $row = $result->fetch_object()) {
            $user = getUser($row->id);
            $json = new stdClass();
            $json->$object_name = $user;

            //ADD NEW JOB
            $job = new stdClass();
            $job->userid = $row->id;
            $job->status = "new";
            $job->content = "created by singer selection service";
            $job_id = addJob($job);
            if ($job_id == "" ) {
                header('HTTP/1.1 500 Internal Server Error');
            } else {
                $json->$object_name->job_id  = $job_id; // Attach New Job ID
                echo json_encode($json,JSON_NUMERIC_CHECK);
            }
        }
    } else {
        die(mysqli_error());
    }
}
else if ( $method == "OPTIONS"){
    header("Access-Control-Allow-Methods: ".implode(",",$methods));
    header("Access-Control-Allow-Headers: content-type");

}
else{
    header("HTTP/1.0 405 Method Not Allowed");
}



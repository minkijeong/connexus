<?php
$object_name = "job";
require "../../../config.php";

function getJob($id){
    global $mysqli;
    $query = " SELECT * FROM job_status WHERE id = ".$id;
    if( $result = $mysqli->query($query)){
        if ( $row = $result->fetch_object()) {
            return extractRow($row);
        }
    }
}

function getAllJob(){
    global $mysqli;
    $query = " SELECT * FROM job_status";
    if( $result = $mysqli->query($query)){
        if ( $row = $result->fetch_object()) {
            return extractRow($row);
        }
    }
}

function extractRow($row){
    $job = new stdClass();
    $job->id = $row->id;
    $job->userid = $row->userid;
    $job->status = $row->status;
    $job->content = isset($row->content)?$row->content:"";
    $job->last_modified = $row->last_modified;
    return $job;
}
$method = $_SERVER['REQUEST_METHOD'];
if ( $method == "GET") {
    header('Content-Type: application/json');
    if ( isset($_GET["id"])){
        $job = getJob($_GET["id"]);
        $json = new stdClass();
        $json->$object_name = $job;
        echo json_encode($json,JSON_NUMERIC_CHECK);
    } else {
        $job = getAllJob();
        $json = new stdClass();
        $json->$object_name = $job;
        echo json_encode($json,JSON_NUMERIC_CHECK);
    }

}
else if ( $method == "POST"){
    header('Content-Type: application/json');
    $json_data = json_decode(file_get_contents("php://input"),false);
    file_put_contents('php://stderr', print_r(file_get_contents("php://input"), TRUE)); //Logging to Apache Log
    $job = $json_data->$object_name;
    $job_id = isset($job->id)? $job->id : 0;
    //echo "INPUT JOB ID : ".$job_id."\n";
    if ($job_id != 0) {
        //UPDATE EXISTING JOB
        $sql = "UPDATE job_status SET status = ?, content = ? WHERE id = ?";
        //echo "$sql \n";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("ssi",
                $job->status,
                $job->content,
                $job->id
            );
            if ($stmt->execute()) {
                $json = new stdClass();
                $json->$object_name = getJob($job->id);
                echo json_encode($json,JSON_NUMERIC_CHECK);
            } else {
                header('HTTP/1.1 500 Internal Server Error');
                echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
            }
            $stmt->close();
        }
    } else {
        //ADD NEW JOB
        $sql = "INSERT INTO job_status ( userid, status, content) VALUES (?,?,?)";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("iss",
                $job->userid,
                $job->status,
                $job->content
            );
            //echo "TEST 1....\n";
            if ($stmt->execute()) {
                $id = $stmt->insert_id;
                //echo "ID: ".$id."\n";
                $json = new stdClass();
                $json->$object_name = getJob($id);
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


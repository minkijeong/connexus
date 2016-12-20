<?php
$object_name = "job";
require "../../../config.php";
require "./job_func.php";

$methods = array("GET","POST");
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
    //$job_content = isset($job->content)? $job->content : "";
    //echo "INPUT JOB ID : ".$job_id."\n";
    if ($job_id != 0) {
        //UPDATE EXISTING JOB
        $sql = "UPDATE job_status SET status = ? WHERE id = ?";
        //echo "$sql \n";
        if ($stmt = $mysqli->prepare($sql)) {
            $stmt->bind_param("si",
                $job->status,
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
        $job_id = addJob($job);
        if ($job_id == "" ) {
            header('HTTP/1.1 500 Internal Server Error');
        } else {
            $json = new stdClass();
            $json->$object_name = getJob($job_id);
            echo json_encode($json,JSON_NUMERIC_CHECK);
        }
    }
}
else if ( $method == "OPTIONS"){
    header("Access-Control-Allow-Methods: ".implode(",",$methods));
    header("Access-Control-Allow-Headers: content-type");

}
else{
    header("HTTP/1.0 405 Method Not Allowed");
}


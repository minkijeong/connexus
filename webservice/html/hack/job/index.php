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

function extractRow($row){
    $job = new stdClass();
    $job->id = $row->id;
    $job->status = $row->status;
    $job->content = isset($row->content)?$row->content:"";
    $job->last_modified = $row->last_modified;
    return $job;
}
$method = $_SERVER['REQUEST_METHOD'];
if ( $method == "GET") {
    if ( isset($_GET["id"])){
        $user = getJob($_GET["id"]);
        $json = new stdClass();
        $json->$object_name = $user;
        echo json_encode($json,JSON_NUMERIC_CHECK);
    }

}
else if ( $method == "POST"){
    $sql = "INSERT INTO job_status(status) values ( 'processing')";

    if( $result = $mysqli->query($sql)){
        $id = $mysqli->insert_id;
        $json = new stdClass();
        $json->$object_name = getJob($id);
        echo json_encode($json,JSON_NUMERIC_CHECK);
    }
}


<?php

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

function addJob($job) {
    global $mysqli;
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
            return $id;
//            $json = new stdClass();
//            $json->$object_name = getJob($id);
//            return json_encode($json, JSON_NUMERIC_CHECK);
        } else {
            return "";
//            return "ERROR: Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        $stmt->close();
    }
    return "";
}
?>
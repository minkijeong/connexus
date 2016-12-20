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
	$user->phone = $row->phone;
	$user->is_song = $row->is_song;
	$user->is_console = $row->is_console;
	$user->is_buyside = $row->is_buyside;
	$user->is_sellside = $row->is_sellside;
	$user->is_mentor = $row->is_mentor;
	return $user;
}

$methods = array("GET","POST","PUT");
$method = $_SERVER['REQUEST_METHOD'];
if ( $method == "GET"){
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
		}
		echo '{"'.$object_name."\":".json_encode($users, JSON_NUMERIC_CHECK)."}";
	}
}
else if ( $method == "POST"){
	$json_data = json_decode(file_get_contents("php://input"),false);
	$user = $json_data->$object_name;
	$is_song = ( isset($user->is_song) && $user->is_song == 1 )? 1:0;
	$is_console = ( isset($user->is_console) && $user->is_console == 1 )? 1:0;

	$is_buyside = ( isset($user->is_buyside) && $user->is_buyside == 1 )? 1:0;
	$is_sellside = ( isset($user->is_sellside) && $user->is_sellside == 1 )? 1:0;
	$is_mentor = ( isset($user->is_mentor) && $user->is_mentor == 1 )? 1:0;

	$sql = "INSERT INTO user ( username, email, phone, is_song, is_console, is_buyside, is_sellside, is_mentor) VALUES".
	       " ('".$user->username."','".$user->email."','".$user->phone."',".$is_song.",".$is_console.",".$is_buyside.",".$is_sellside.",".$is_mentor.")";
	if( $result = $mysqli->query($sql)){
		$id = $mysqli->insert_id;
		$json = new stdClass();
		$json->$object_name = getUser($id);
		echo json_encode($json,JSON_NUMERIC_CHECK);
	}

}
else if ( $method == "PUT"){
	header("HTTP/1.0 405 Method Not Allowed");
}
else{
	header("HTTP/1.0 405 Method Not Allowed");
}



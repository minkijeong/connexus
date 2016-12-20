<?php
$CNF = new StdClass();
$CNF->DB_HOST = "localhost";
$CNF->DB_USER = "root";
$CNF->DB_PASS = "minki";

$mysqli = new mysqli("localhost", "root", "minki", "wip");
if ($mysqli->connect_errno) {
        echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
        return;
}

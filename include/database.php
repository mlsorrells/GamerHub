<?php
session_start();
$dbhost = "localhost";
$dbname = "root";
$dbpass = "";
$dbdb = "gamerhub";
$conn = new mysqli($dbhost,$dbname,$dbpass,$dbdb);
if($conn->connect_error){
die("Failed to connect");
}
$user_num_query = $conn->query("SELECT * FROM users");
$newest_user_query = $conn->query("SELECT username FROM users ORDER BY id DESC LIMIT 1");
$news_query = $conn->query("SELECT * FROM news ORDER BY id DESC LIMIT 1");
?>
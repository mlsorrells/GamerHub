<?php
require "database.php";
if(isset($_SESSION['username'])) {
	$username = $_SESSION['username'];
} else {
	$username = 'guest';
}
date_default_timezone_set("America/New_York");
if($username != "guest") {
	$get_query = $conn->query("SELECT * FROM users WHERE username='$_SESSION[username]'");
	$user = $get_query->fetch_assoc();
}
$user_num = $user_num_query->num_rows;
if(isset($_SESSION['username'])) {
	if($user['banned'] == "1") {
		header('location:/banned.php');
	}

	if($user['ipbanned'] == "1") {
		header('location:/banned.php');
	}
}
?>
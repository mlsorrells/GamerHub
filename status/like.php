<?php
require $_SERVER['DOCUMENT_ROOT']."/include/session.php";
$id = $_GET['id'];
$query = $conn->query("SELECT * FROM status WHERE id='$id'");
$check = $query->num_rows;
if($check == "0"){
header('location:index.php');
}else{
$insert = $conn->query("INSERT INTO status_likes(username, status_id) VALUES('$username','$id')");
header('location:index.php');
}
?>
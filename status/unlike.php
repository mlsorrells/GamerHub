<?php
require $_SERVER['DOCUMENT_ROOT']."/include/session.php";
$id = $_GET['id'];
$query = $conn->query("SELECT * FROM status_likes WHERE id='$id' AND username='$username'");
$check = $query->num_rows;
if($check == "1"){
$delete = $conn->query("DELETE FROM status_likes WHERE id='$id'");
header('location:index.php');
}else{
header('location:index.php');
}
?>
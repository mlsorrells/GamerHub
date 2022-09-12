<?php
require $_SERVER['DOCUMENT_ROOT']."/include/session.php";
$id = $_GET['id'];
$query = $conn->query("SELECT * FROM newsletter WHERE id='$id'");
$check = $query->num_rows;
if($check == "0"){
header('location:index.php');
}else{
$delete = $conn->query("DELETE FROM newsletter WHERE id='$id'");
header('location:index.php');
}
?>
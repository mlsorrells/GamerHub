<?php
require $_SERVER['DOCUMENT_ROOT']."/include/session.php";
if(isset($_POST['submit'])){
$status = $_POST['status'];
$status = strip_tags($status);
$status = htmlentities($status);
$status = $conn->real_escape_string($status);
$date = date('y-m-d');
if($status == ""){
$fields = "Please fill in the field.";
}else{
$insert = $conn->query("INSERT INTO status(username, message, date)
VALUES('$username','$status','$date')");
$success = "Status successfully posted!";
}
}
?>
<html>
<head>
<meta name="viewport" content="width=device-width"/>
<link rel="stylesheet" type="text/css" href="/gamerhub.css"/>
<link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.css"/>
<title>GamerHub - Status</title>
</head>
<body align="center">
<?php include $_SERVER['DOCUMENT_ROOT']."/include/header.php"; ?>
<h3>Status</h3>
<div class="inner">
<?php
if(isset($_SESSION['username'])) {
if(isset($fields)) {
    echo $fields;
}
if(isset($success)) {
    echo $success;
}
?>
<form action="index.php" method="post">
<b>Post a status</b><br/>
<textarea name="status" rows="5" cols="20"></textarea><br/>
<input type="submit" name="submit" value="Post"/>
</form>
<?php
}else{
echo "<b>You must be logged in to post a status.</b>";
}
?>
</div>
<?php
$status_query = $conn->query("SELECT * FROM status ORDER BY id DESC LIMIT 15");
while($status = $status_query->fetch_assoc()){
echo '<div class="inner">';
echo '<b><a href="/user/profile.php?user='.$status['username'].'">'.$status['username'].'</b></a><br/>';
echo '('.$status['date'].')<br/>';
echo $status['message'].'<br/>';
$like_del_query = $conn->query("SELECT * FROM status_likes WHERE status_id='$status[id]' AND username='$username'");
$like_del = $like_del_query->fetch_assoc();
$like_check_query = $conn->query("SELECT * FROM status_likes WHERE status_id='$status[id]' AND username='$username'");
$like_check = $like_check_query->num_rows;
if($like_check == "0"){
echo '<div class="like" style="float:left" onclick="window.location=\'like.php?id='.$status['id'].'\'">';
echo 'Like (';
}elseif($like_check == "1"){
echo '<div class="like" style="float:left" onclick="window.location=\'unlike.php?id='.$like_del['id'].'\'">';
echo 'Unlike (';
}
$like_query = $conn->query("SELECT * FROM status_likes WHERE status_id='$status[id]'");
$like_count = $like_query->num_rows;
echo $like_count;
echo ')</div>';
echo '<div class="like" style="float:right" onclick="alert(\'Not ready yet\')">Likes</div>';
if($user['level'] == "3" || $user['level'] == "2"){
echo '<a href="delete.php?id='.$status['id'].'">Delete</a>';
}
echo '</div>';
}
include $_SERVER['DOCUMENT_ROOT']."/include/footer.php";
?>
</body>
</html>
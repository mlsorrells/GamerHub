<?php
require $_SERVER['DOCUMENT_ROOT']."/include/session.php";
$getuser = $_GET['user'];
$query = $conn->query("SELECT * FROM users WHERE username='$getuser'");
$user_info = $query->fetch_assoc();
$user_check = $query->num_rows;
?>
<html>
<head>
<meta name="viewport" content="width=device-width"/>
<link rel="stylesheet" type="text/css" href="/gamerhub.css"/>
<link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.css"/>
<title><?php
if($user_check == "0"){
echo "GamerHub";
}else{
echo "GamerHub - ".$user_info['username']."'s Profile";
}
?></title>
</head>
<body align="center">
<?php
include $_SERVER['DOCUMENT_ROOT']."/include/header.php";
if($user_check == "0"){
echo '<div class="inner">';
echo 'This user does not exist.';
echo '</div>';
}else{
echo '<div class="inner">';
echo "<h3>".$user_info['username']."'s Profile ";
if($user_info['username'] == $_SESSION['username']){
echo '(<a href="editprofile.php?user='.$user_info['username'].'">Edit</a>)';
}
echo "</h3>";
echo '<b>User ID: '.$user_info['id'].'</b><br/><br/>';
echo "<b>About me</b><br/>";
if(empty($user_info['about'])){
echo "This user has not filled this out yet.";
}else{
echo $user_info['about'];
}
echo "<br/><br/>";
if($user_info['level'] != "1"){
echo "<b>".$user_info['username']."'s Priveleges</b><br/>";
if($user_info['level'] == "2"){
echo "Moderator";
}elseif($user_info['level'] == "3"){
echo "Administrator";
}
}
echo '</div>';
?>
<div class="inner">
<form method="post">
<?php
$user_id = $user_info['id'];
if(isset($_POST['submit'])){
$content = $_POST['content'];
$content = strip_tags($content);
$content = htmlentities($content);
$content = $conn->real_escape_string($content);
$date = date('y-m-d');
if($content == ""){
echo "Nothing entered in the field.<br/>";
}else{
$insert = $conn->query("INSERT INTO comments(username, date, content, user_id) VALUES('$username','$date','$content','$user_id')");
echo "Successfully posted comment.<br/>";
}
}
if($username){
?>
<b>Post a comment</b><br/>
<textarea name="content" rows="5" cols="20"></textarea><br/>
<input type="submit" name="submit" value="Post comment"/>
<?php
}else{
echo '<br/><b>You must be logged in to post a comment.</b>';
}
?>
</form>
</div>
<?php
$comments_query = $conn->query("SELECT * FROM comments WHERE user_id='$user_id' ORDER BY id DESC");
while($comments = $comments_query->fetch_assoc()){
echo '<div class="inner">';
echo '<b><a href="profile.php?user='.$comments['username'].'">'.$comments['username'].'</a></b><br/>';
echo '('.$comments['date'].')<br/>';
echo $comments['content'];
echo '</div>';
}
?>
<?php
}
include $_SERVER['DOCUMENT_ROOT']."/include/footer.php";
?>
</body>
</html>
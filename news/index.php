<?php
require $_SERVER['DOCUMENT_ROOT']."/include/session.php";
if(isset($_POST['submit'])){
$subject = $_POST['subject'];
$subject = stripslashes($subject);
$subject = $conn->real_escape_string($subject);
$content = $_POST['content'];
$content = stripslashes($content);
$content = $conn->real_escape_string($content);
if($subject == "" || $content == ""){
$fields = "All fields are required.";
}else{
$insert = $conn->query("INSERT INTO news(username, subject, content) VALUES('$username','$subject','$content')");
$success = "News successfully posted.";
}
}
?>
<html>
<head>
<meta name="viewport" content="width=device-width"/>
<link rel="stylesheet" type="text/css" href="/gamerhub.css"/>
<link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.css"/>
<title>GamerHub - News</title>
</head>
<body align="center">
<?php include $_SERVER['DOCUMENT_ROOT']."/include/header.php"; ?>
<h3>News</h3>
<?php
if($user['level'] == "3"){
if(isset($fields)) {
    echo $fields;
}
if(isset($success)) {
    echo $success;
}
?>
<div class="inner">
<form action="" method="post">
<strong>Subject</strong><br/>
<input type="text" name="subject"/><br/>
<strong>Content</strong><br/>
<textarea name="content" rows="6" cols="25"/></textarea><br/>
<input type="submit" name="submit" value="Post news"/>
</form>
</div>
<?php
}
$query = $conn->query("SELECT * FROM news ORDER BY id DESC");
while($news = $query->fetch_assoc()){
echo '<div class="inner">';
echo '<strong><font size="4">'.$news['subject'].'</font></strong><br/>';
echo 'By '.$news['username'].'<br/>';
if($user['level'] == "3"){
echo '[<a href="delete.php?id='.$news['id'].'">Delete</a>]<br/>';
}else{
echo '<br/>';
}
echo $news['content'];
echo '</div>';
}
include $_SERVER['DOCUMENT_ROOT']."/include/footer.php";
?>
</body>
</html>
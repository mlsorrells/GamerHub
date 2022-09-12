<?php
require $_SERVER['DOCUMENT_ROOT']."/include/session.php";
if(isset($_POST['submit'])){
$subject = $_POST['subject'];
$subject = stripslashes($subject);
htmlentities($subject);
$subject = $conn->real_escape_string($subject);
$content = $_POST['content'];
$content = stripslashes($content);
htmlentities($content);
$content = $conn->real_escape_string($content);
if($subject == "" || $content == ""){
$fields = "All fields are required.";
}else{
$insert = $conn->query("INSERT INTO newsletter(username, subject, content) VALUES('$username','$subject','$content')");
$success = "Gamer news successfully posted.";
}
}
?>
<html>
<head>
<meta name="viewport" content="width=device-width"/>
<link rel="stylesheet" type="text/css" href="/gamerhub.css"/>
<link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.css"/>
<title>GamerHub - Newsletter</title>
</head>
<body align="center">
<?php include $_SERVER['DOCUMENT_ROOT']."/include/header.php"; ?>
<h3>Gaming Newsletter</h3>
<?php
if($user['level'] == "2" || $user['level'] == "3"){
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
<input type="submit" name="submit" value="Post gamer news"/>
</form>
</div>
<?php
}
$query = $conn->query("SELECT * FROM newsletter ORDER BY id DESC");
while($newsletter = $query->fetch_assoc()){
echo '<div class="inner">';
if(isset($selected)) {
    echo $selected;
}
echo '<font size="4"><b>'.$newsletter['subject'].'</b></font><br/>';
echo 'By '.$newsletter['username'].'<br/>';
if($user['level'] == "3"){
echo '[<a href="delete.php?id='.$newsletter['id'].'">Delete</a>]<br/>';
}else{
echo '<br/>';
}
echo $newsletter['content'];
echo '</div>';
}
include $_SERVER['DOCUMENT_ROOT']."/include/footer.php";
?>
</body>
</html>
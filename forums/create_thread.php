<?php
require $_SERVER['DOCUMENT_ROOT']."/include/session.php";
$get_forum = $_GET['forum'];
$get_query = $conn->query("SELECT * FROM categories WHERE id='$get_forum'");
$forum = $get_query->fetch_assoc();
$forum_check = $get_query->num_rows;
if($forum_check == "0"){
header('location:index.php');
}else{
if(isset($_POST['create'])){
$name = $_POST['name'];
$name = strip_tags($name);
$name = htmlentities($name);
$name = $conn->real_escape_string($name);
$content = $_POST['content'];
$content = strip_tags($content);
$content = htmlentities($content);
$content = $conn->real_escape_string($content);
$date = date('y-m-d h:i:s');
if($name == "" || $content == ""){
$fields = "All fields are required.";
}else{
$insert1 = $conn->query("INSERT INTO threads(name, date_time, user, cat_id) VALUES('$name','$date','$username','$get_forum')");
if($insert1){
$last_id = $conn->insert_id;
$insert2 = $conn->query("INSERT INTO posts(content, date_time, user, thread_id) VALUES('$content','$date','$username','$last_id')");
header("location:threads.php?forum=$get_forum");
}
}
}
?>
<html>
<head>
<meta name="viewport" content="width=device-width"/>
<link rel="stylesheet" type="text/css" href="/gamerhub.css"/>
<link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.css"/>
<title>GamerHub - Create Thread</title>
</head>
<body align="center">
<?php include $_SERVER['DOCUMENT_ROOT']."/include/header.php"; ?>
<h3>Create a New Thread</h3>
<div class="inner">
<form action="" method="post">
<label for="name">Name</label><br/>
<input type="text" name="name" id="name"/><br/>
<label for="content">Content</label><br/>
<textarea name="content" id="content" rows="5" cols="30"></textarea><br/>
<input type="submit" name="create" value="Create Forum"/>
</form>
</div>
<?php include $_SERVER['DOCUMENT_ROOT']."/include/footer.php"; ?>
</body>
</html>
<?php
}
?>
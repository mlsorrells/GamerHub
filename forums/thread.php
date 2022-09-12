<?php
require $_SERVER['DOCUMENT_ROOT']."/include/session.php";
$get_thread = $_GET['thread'];
$get_query = $conn->query("SELECT * FROM threads WHERE id='$get_thread'");
$thread = $get_query->fetch_assoc();
$thread_check = $get_query->num_rows;
if($thread_check == "0"){
header('location:index.php');
}else{
?>
<html>
<head>
<meta name="viewport" content="width=device-width"/>
<link rel="stylesheet" type="text/css" href="/gamerhub.css"/>
<link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.css"/>
<title>GamerHub - Forums</title>
</head>
<body align="center">
<?php include $_SERVER['DOCUMENT_ROOT']."/include/header.php"; ?>
<br/>
<?php
$posts_query = $conn->query("SELECT * FROM posts WHERE thread_id='$thread[id]'");
echo '<div class="inner">';
echo $thread['name'].'<br/>';
echo 'Started by '.$thread['user'];
echo '</div>';
?>
<?php include $_SERVER['DOCUMENT_ROOT']."/include/footer.php"; ?>
</body>
</html>
<?php
}
?>
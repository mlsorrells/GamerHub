<?php
require $_SERVER['DOCUMENT_ROOT']."/include/session.php";
$get_forum = $_GET['forum'];
$get_query = $conn->query("SELECT * FROM categories WHERE id='$get_forum'");
$forum = $get_query->fetch_assoc();
$forum_check = $get_query->num_rows;
if($forum_check == "0"){
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
<h3><?php echo $forum['name']; ?></h3>
<div class="inner">
<a href="create_thread.php?forum=<?php echo $forum['id']; ?>">Create a new thread</a>
</div>
<br/>
<a href="index.php">Back to forum menu</a><br/>
<?php
$threads_query = $conn->query("SELECT * FROM threads WHERE cat_id='$forum[id]'");
$threads_check = $threads_query->num_rows;
if($threads_check == "0"){
echo '<div class="inner">';
echo 'No threads have been made in this forum yet.';
echo '</div>';
}else{
while($thread = $threads_query->fetch_assoc()){
echo '<div class="inner" align="left">';
echo '<a href="thread.php?thread='.$thread['id'].'">'.$thread['name'].'</a><br/>';
echo 'Started by: <a href="/user/profile.php?user='.$thread['user'].'">'.$thread['user'].'</a><br/>';
$last_post_query = $conn->query("SELECT * FROM posts WHERE thread_id='$thread[id]' ORDER BY id DESC LIMIT 1");
$last_post = $last_post_query->fetch_assoc();
echo 'Last post by: <a href="/user/profile.php?user='.$thread['user'].'">'.$last_post['user'].'</a>';
echo '</div>';
}
}
include $_SERVER['DOCUMENT_ROOT']."/include/footer.php";
?>
</body>
</html>
<?php
}
?>
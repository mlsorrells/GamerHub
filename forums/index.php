<?php
require $_SERVER['DOCUMENT_ROOT']."/include/session.php";
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
<h3>Forums</h3>
<?php
if($user['level'] == "3"){
echo '<div class="inner">';
echo '<a href="create_forum.php">Create a new forum</a>';
echo '</div><br/>';
}
$forum_query = $conn->query("SELECT * FROM categories");
while($forum = $forum_query->fetch_assoc()){
echo '<div class="inner" align="left">';
echo '<a href="threads.php?forum='.$forum['id'].'">'.$forum['name'].'</a><br/>';
echo $forum['description'];
echo '</div>';
}
include $_SERVER['DOCUMENT_ROOT']."/include/footer.php"; ?>
</body>
</html>
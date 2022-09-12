<?php
require $_SERVER['DOCUMENT_ROOT']."/include/session.php";
if(isset($_POST['submit'])){
$link_name = $_POST['link_name'];
$link_name = strip_tags($link_name);
$link_name = htmlentities($link_name);
$link_name = $conn->real_escape_string($link_name);
$link_url = $_POST['link_url'];
$link_url = strip_tags($link_url);
$link_url = htmlentities($link_url);
$link_url = $conn->real_escape_string($link_url);
$query = $conn->query("SELECT * FROM links WHERE url='$link_url'");
$count = $query->num_rows;
if(empty($link_name) || empty($link_url)){
$fields = 'All fields are required.';
}elseif($count == "1"){
$taken = 'This link was already submitted.';
}else{
$insert = $conn->query("INSERT INTO links(name, url, username) VALUES('$link_name','$link_url','$username')");
$success = 'Successfully submitted link.';
}
}
?>
<html>
<head>
<meta name="viewport" content="width=device-width"/>
<link rel="stylesheet" type="text/css" href="/gamerhub.css"/>
<link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.css"/>
<title>GamerHub - Links</title>
</head>
<body align="center">
<?php include $_SERVER['DOCUMENT_ROOT']."/include/header.php"; ?>
<h3>Links</h3>
<div class="inner">
<?php
$query2 = $conn->query("SELECT * FROM links");
$num = $query2->num_rows;
if(isset($fields)) {
    echo $fields;
}
if(isset($taken)) {
    echo $taken;
}
if(isset($success)) {
    echo $success;
}
echo '<br/>';
if($num == "0"){
echo 'No links have been submitted yet.';
}else{
while($link = $query2->fetch_assoc()){
echo '<a href="http://'.$link['url'].'">'.$link['name'].'</a> (by '.$link['username'].')';
if($user['level'] == "2" || $user['level'] == "3"){
echo ' [<a href="delete.php?id='.$link['id'].'">Delete</a>]';
}
echo '<br/>';
}
}
?>
<hr/>
<b>Submit a link</b><br/>
<?php
if(!$username){
echo 'You must be logged in to submit a link.<br/>';
}else{
?>
<form action="" method="post">
Name: <input type="text" name="link_name"/><br/>
URL: http://<input type="text" name="link_url"/><br/>
<input type="submit" name="submit" value="Submit link"/><br/>
</form>
<?php
}
?><br/>
<b>Rules before submitting a link</b><br/>
1) Don't post any innapropriate sites.<br/>
2) The site must be owned by you, or you must be a developer of the site.<br/>
3) The site must be functional/working.  Don't post broken sites.<br/>
4) The site has to be usable.
<p>Failure to follow these rules will result in a mod removing the link, and possibly a ban.</p>
</div>
<?php include $_SERVER['DOCUMENT_ROOT']."/include/footer.php"; ?>
</body>
</html>
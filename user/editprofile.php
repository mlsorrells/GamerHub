<?php
require $_SERVER['DOCUMENT_ROOT']."/include/session.php";
if(isset($_POST['submit'])){
$about = $_POST['about'];
$about = stripslashes($about);
$about = htmlentities($about);
$about = $conn->real_escape_string($about);
$update = $conn->query("UPDATE users SET about='$about' WHERE username='$username'");
$success = 'Profile updated successfully!<br/><a href="profile.php?user='.$username.'">Back to profile</a>';
}
?>
<html>
<head>
<meta name="viewport" content="width=device-width"/>
<link rel="stylesheet" type="text/css" href="/gamerhub.css"/>
<link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.css"/>
<title>GamerHub - Edit Profile</title>
</head>
<body align="center">
<?php include $_SERVER['DOCUMENT_ROOT']."/include/header.php"; ?>
<h3>Edit Profile</h3>
<?php
    if(isset($success)) {
        echo $success;
    }
?>
<div class="inner">
<form action="" method="post">
<b>About me</b><br/>
<textarea name="about" rows=8" cols="30"/><?php echo $user['about']; ?></textarea><br/><br/>
<input type="submit" name="submit" value="Edit Profile">
</form>
</div>
<?php include $_SERVER['DOCUMENT_ROOT']."/include/footer.php"; ?>
</body>
</html>
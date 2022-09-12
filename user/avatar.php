<?php
require $_SERVER['DOCUMENT_ROOT']."/include/session.php";
?>
<html>
<head>
<meta name="viewport" content="width=device-width"/>
<link rel="stylesheet" type="text/css" href="/gamerhub.css"/>
<link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.css"/>
<title>GamerHub - Change Avatar</title>
</head>
<body align="center">
<?php include $_SERVER['DOCUMENT_ROOT']."/include/header.php"; ?>
<h2>Change Avatar</h2>
<div class="inner">
<a href="editprofile.php">Back to Edit Profile</a><br/><br/>
<form action="" method="post" enctype="multipart/form-data">
<input type="file" name="avatar" id="avatar"/><br/>
<input type="submit" name="change" value="Change Avatar"/>
</div>
<?php include $_SERVER['DOCUMENT_ROOT']."/include/footer.php"; ?>
</body>
</html>
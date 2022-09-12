<?php
require "include/session.php";
if(isset($_SESSION['username'])){
header("Location:index.php");
}
if(isset($_POST['login'])){
$username = $_POST['username'];
$username = stripslashes($username);
$username = $conn->real_escape_string($username);
$password = $_POST['password'];
$password = stripslashes($password);
$password = $conn->real_escape_string($password);
$password = hash('sha256', $password);
$select = $conn->query("SELECT * FROM users WHERE username='$username' AND password='$password'");
$count = $select->num_rows;
if($username == "" || $password == ""){
echo "All fields are required.";
}elseif($count === 1){
$_SESSION['username'] = $username;
header("Location:index.php");
}else{
echo 'Invalid username/password.';
}
}
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="gamerhub.css"/>
<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css"/>
<meta name="viewport" content="width=device-width"/>
<title>GamerHub - Login</title>
</head>
<body align="center">
<?php include "include/header.php"; ?>
<h3>Login</h3>
<div class="inner">
<form action="login.php" method="post">
<table>
<tr>
<td>Username:</td><td><input type="text" name="username" id="username"/></td>
</tr>
<tr>
<td>Password:</td><td><input type="password" name="password" id="password"/></td>
</tr>
<tr>
<td><input type="submit" name="login" value="Login"/></td>
</tr>
</table>
</form>
</div>
<?php include "include/footer.php"; ?>
</body>
</html>
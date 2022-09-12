<?php
require "include/session.php";
$logged = $_SESSION['username'];
if($logged){
header("Location:index.php");
}
if(isset($_POST['register'])){
$username = $_POST['username'];
$username = stripslashes($username);
$username = $conn->real_escape_string($username);
$password = $_POST['password'];
$password = stripslashes($password);
$password = $conn->real_escape_string($password);
$password = hash('sha256', $password);
$rpass = $_POST['rpass'];
$rpass = stripslashes($rpass);
$rpass = $conn->real_escape_string($rpass);
$rpass = hash('sha256', $rpass);
$email = $_POST['email'];
$email = stripslashes($email);
$email = $conn->real_escape_string($email);
$email = filter_var($email, FILTER_SANITIZE_EMAIL);
$ip = $_SERVER['REMOTE_ADDR'];
$level = "1";
$select = $conn->query("SELECT * FROM users WHERE username = '$username'");
$count = $select->num_rows;
$select2 = $conn->query("SELECT * FROM banned_ips WHERE ip='$ip'");
$count2 = $select2->num_rows;
if($username == "" || $password == "" || $rpass == "" || $email == ""){
$fields = "All fields are required.";
}elseif($password !== $rpass){
$pass = "The two password fields do not match.  Please try again.";
}elseif(!filter_var($email, FILTER_VALIDATE_EMAIL) === true){
$invalid_email = "Invalid email.";
}elseif($count2 == "1"){
$ipbanned = "This IP is banned.";
}elseif($count === "1"){
$checked = "That username is already taken.  Please choose a different one.";
}else{
$insert = $conn->query("INSERT INTO users(username, password, email, ip, level) VALUES('$username','$password','$email','$ip','$level')");
$success = 'Registered successfully!  You may now <a href="login.php">login</a>';
}
}
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="gamerhub.css"/>
<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css"/>
<meta name="viewport" content="width=device-width"/>
<title>GamerHub - Register</title>
</head>
<body align="center">
<?php include "include/header.php"; ?>
<h3>Register</h3>
<div class="inner">
<form action="register.php" method="post">
<?php
echo $fields;
echo $pass;
echo $ipbanned;
echo $invalid_email;
echo $checked;
echo $success;
?>
<table>
<tr>
<td>Username:</td><td><input type="text" name="username" id="username"/></td>
</tr>
<tr>
<td>Password:</td><td><input type="password" name="password" id="password"/></td>
</tr>
<tr>
<td>Confirm Password:</td><td><input type="password" name="rpass" id="rpass"/></td>
</tr>
<tr>
<td>Email:</td><td><input type="email" name="email" id="email"/></td>
</tr>
<tr>
<td></td>
<td><input type="submit" name="register" value="Register"/></td>
</tr>
</table>
</form>
</div>
<?php include "include/footer.php"; ?>
</body>
</html>
<?php
require $_SERVER['DOCUMENT_ROOT']."/include/session.php";
/*
ChatBox 6.1.6
Latest update 2013-08-01 06:35
ahmad.murey@gmail.com
www.zaksdg.com
*/

require_once("./core.php");

$username=isset($_POST["username"])?trim($_POST["username"]):"";
$display=isset($_POST["display"])?trim($_POST["display"]):"";
if($username!=""){
	$_SESSION[USERNAME]=$username;
	$_SESSION[USER_DISPLAY]=getAlias($username, $display);
	$_SESSION[USER_AUTH]=authAlgo($username);
	header("Location: .");
}
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/gamerhub.css" />
<link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.css"/>
<title>GamerHub - Chat Rules</title>
<meta name="viewport" content="width=device-width"/>
</head>
<body align="center">
<?php include $_SERVER['DOCUMENT_ROOT']."/include/header.php"; ?>
<h3>Chat Rules</h3>
<div class="inner">
<!-- 
Rules by Bahamut.
Made on March 22, 2015.
7:26 PM Central Time
-->
<p>1) <b>Do not spam any form of inappropriate content.</b>
<br />
2) <b>In adjacent with rule one(# 1), do <u>NOT</u> spam anything in the chats. A spam here is either random topics all said within a minute, and/ or ten msgs in a time of 1.5(one minute, thirty seconds) minutes.</b>
<br />
3) <b>Do not advertise any form of content on the chat, without permission from the Administrators and Moderators. </b>
<br />
4) <b>Do not act inappropriate or use inappropriate language on here, as there are children.</b>
<br />
5) <b>Do not use inappropriate names, or change your name without notifying the staff(admins and mods and/ or member developers are excused from this rule) first.</b>
<br />
6) <b>Do not ask to become a mod, as members who meet our terms may and will be approached.</b>
<br />
7) <b>Be respective, even if you are loosing a argument. Chats have died due to drama.</b>
</div>
<br />
<div class="inner">
<form method="post">
	<p>
		Username <input type="text" name="username" value="<?php
if($_SESSION['username']){
echo $_SESSION['username'];
}else{
echo "guest";
}
?>" readonly />
		<input type="submit" value="Sign in" />
	</p>
</form>
</div>
</div>
<?php include $_SERVER['DOCUMENT_ROOT']."/include/footer.php"; ?>
</body>
</html>
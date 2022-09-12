<?php
require $_SERVER['DOCUMENT_ROOT']."/include/session.php";
if($user['level'] != "2"){
header('location:/index.php');
}
?>
<html>
<head>
<meta name="viewport" content="width=device-width"/>
<link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="/gamerhub.css"/>
<title>GamerHub - Mod Panel</title>
</head>
<body align="center">
<?php include $_SERVER['DOCUMENT_ROOT']."/include/header.php"; ?>
<h3>Moderator Panel</h3>
<div class="inner">
<font size="4"><b>Ban user</b></font><br/>
<form method="post">
<?php
if(isset($_POST['ban'])){
$ban_user = $_POST['ban_user'];
if($ban_user == "Select username"){
echo 'You must select a username.';
}else{
$ban = $conn->query("UPDATE users SET banned='1' WHERE username='$ban_user'");
echo 'User banned.';
}
}
$query1 = $conn->query("SELECT * FROM users WHERE banned='0'");
?>
<table>
<tr>
<td><select name="ban_user" style="width:300px">
<option>Select username</option>
<?php
while($list1 = $query1->fetch_assoc()){
echo '<option value="'.$list1['username'].'">'.$list1['username'].'</option>';
}
?></td>
</tr>
<tr>
<td><input type="submit" name="ban" value="Ban user"/></td>
</tr>
</table>
</form>
</div>
<div class="inner">
<font size="4"><b>Unban user</b></font><br/>
<form method="post">
<?php
if(isset($_POST['unban'])){
$unban_user = $_POST['unban_user'];
if($unban_user == "Select username"){
echo 'You must select a username.';
}else{
$unban = $conn->query("UPDATE users SET banned='0' WHERE username='$unban_user'");
echo 'User unbanned.';
}
}
$query2 = $conn->query("SELECT * FROM users WHERE banned='1'");
?>
<table>
<tr>
<td><select name="unban_user" style="width:300px">
<option>Select username</option>
<?php
while($list2 = $query2->fetch_assoc()){
echo '<option value="'.$list2['username'].'">'.$list2['username'].'</option>';
}
?></td>
</tr>
<tr>
<td><input type="submit" name="unban" value="Unban user"/></td>
</tr>
</table>
</form>
</div>
<div class="inner">
<font size="4"><b>IP ban user</b></font><br/>
<form method="post">
<?php
if(isset($_POST['ipban'])){
$ipban_user = $_POST['ipban_user'];
if($ipban_user == "Select username"){
echo 'You must select a username.';
}else{
$ipban = $conn->query("INSERT INTO banned_ips(ip) VALUES('$ipban_user')");
$ipban2 = $conn->query("UPDATE users SET ipbanned='1' WHERE ip='$ipban_user'");
echo 'User IP banned.';
}
}
$query3 = $conn->query("SELECT * FROM users WHERE ipbanned='0'");
?>
<table>
<tr>
<td><select name="ipban_user" style="width:300px">
<option>Select username</option>
<?php
while($list3 = $query3->fetch_assoc()){
echo '<option value="'.$list3['ip'].'">'.$list3['username'].'</option>';
}
?></td>
</tr>
<tr>
<td><input type="submit" name="ipban" value="IP ban user"/></td>
</tr>
</table>
</form>
</div>
<div class="inner">
<font size="4"><b>IP unban user</b></font><br/>
<form method="post">
<?php
if(isset($_POST['ipunban'])){
$ipunban_user = $_POST['ipunban_user'];
if($ipunban_user == "Select username"){
echo 'You must select a username.';
}else{
$ipunban = $conn->query("DELETE FROM banned_ips WHERE ip='$ipunban_user'");
$ipunban2 = $conn->query("UPDATE users SET ipbanned='0' WHERE ip='$ipunban_user'");
echo 'User IP unbanned.';
}
}
$query4 = $conn->query("SELECT * FROM users WHERE ipbanned='1'");
?>
<table>
<tr>
<td><select name="ipunban_user" style="width:300px">
<option>Select username</option>
<?php
while($list4 = $query4->fetch_assoc()){
echo '<option value="'.$list4['ip'].'">'.$list4['username'].'</option>';
}
?></td>
</tr>
<tr>
<td><input type="submit" name="ipunban" value="IP unban user"/></td>
</tr>
</table>
</form>
</div>
<?php include $_SERVER['DOCUMENT_ROOT']."/include/footer.php"; ?>
</body>
</html>
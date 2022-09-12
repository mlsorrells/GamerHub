<?php
require $_SERVER['DOCUMENT_ROOT']."/include/session.php";
$query = $conn->query("SELECT * FROM users");
if($user['level'] != "3"){
header('location:/index.php');
}
?>
<html>
<head>
<meta name="viewport" content="width=device-width"/>
<link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.css"/>
<link rel="stylesheet" type="text/css" href="/gamerhub.css"/>
<title>GamerHub - Admin Panel</title>
</head>
<body align="center">
<?php include $_SERVER['DOCUMENT_ROOT']."/include/header.php"; ?>
<h3>Administrator Panel</h3>
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
$ban = $conn->query("UPDATE users SET banned='0' WHERE username='$unban_user'");
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
<div class="inner">
<font size="4"><b>Promote user</b></font>
<form method="post">
<?php
if(isset($_POST['promote'])){
$promote_user = $_POST['promote_user'];
$promote_rank = $_POST['promote_rank'];
if($promote_user == "Select username"){
echo 'You must select a username.';
}elseif($promote_rank == "Select rank"){
echo 'You must select a rank.';
}else{
$promote = $conn->query("UPDATE users SET level='$promote_rank' WHERE username='$promote_user'");
echo 'User promoted.';
}
}
$query5 = $conn->query("SELECT * FROM users WHERE level='1'");
?>
<table>
<tr>
<td><select name="promote_user" style="width:300px">
<option>Select username</option>
<?php
while($list5 = $query5->fetch_assoc()){
echo '<option value="'.$list5['username'].'">'.$list5['username'].'</option>';
}
?></select></td>
</tr>
<tr>
<td><select name="promote_rank" style="width:300px">
<option>Select rank</option>
<option value="2">Moderator</option>
<option value="3">Administrator</option>
</select></td>
</tr>
<tr>
<td><input type="submit" name="promote" value="Promote user"/></td>
</tr>
</table>
</form>
</div>
<div class="inner">
<font size="4"><b>Demote user</b></font>
<form method="post">
<?php
if(isset($_POST['demote'])){
$demote_user = $_POST['demote_user'];
if($demote_user == "Select username"){
echo 'You must select a username.';
}else{
$demote = $conn->query("UPDATE users SET level='1' WHERE username='$demote_user'");
echo 'User demoted.';
}
}
$query6 = $conn->query("SELECT * FROM users WHERE level!='1'");
?>
<table>
<tr>
<td><select name="demote_user" style="width:300px">
<option>Select username</option>
<?php
while($list6 = $query6->fetch_assoc()){
echo '<option value="'.$list6['username'].'">'.$list6['username'].'</option>';
}
?></select></td>
</tr>
<tr>
<td><input type="submit" name="demote" value="Demote user"/></td>
</tr>
</table>
</form>
</div>
<div class="inner">
<font size="4"><b>Delete user</b></font>
<form method="post">
<?php
if(isset($_POST['delete'])){
$delete_user = $_POST['delete_user'];
if($delete_user == "Select username"){
echo 'You must select a username';
}else{
$demote = $conn->query("DELETE FROM users WHERE username='$delete_user'");
echo 'User deleted.';
}
}
$query7 = $conn->query("SELECT * FROM users");
?>
<table>
<tr>
<td><select name="delete_user" style="width:300px">
<option>Select username</option>
<?php
while($list7 = $query7->fetch_assoc()){
echo '<option value="'.$list7['username'].'">'.$list7['username'].'</option>';
}
?>
</select></td>
</tr>
<tr>
<td><input type="submit" name="delete" value="Delete user"/></td>
</tr>
</table>
</form>
</div>
<table width="100%" border="1">
<tr>
<th>Username</th><th>IP</th>
</tr>
<?php
while($list = $query->fetch_assoc()){
echo '<tr>';
echo '<td width="50%"><a href="/user/profile.php?user='.$list['username'].'">'.$list['username'].'</a></td>';
echo '<td width="50%">'.$list['ip'].'</td>';
echo '</tr>';
}
?>
</table>
<?php include $_SERVER['DOCUMENT_ROOT']."/include/footer.php"; ?>
</body>
</html>
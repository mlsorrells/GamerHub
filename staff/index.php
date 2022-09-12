<?php
require $_SERVER['DOCUMENT_ROOT']."/include/session.php";
$mod_query = $conn->query("SELECT * FROM users WHERE level='2'");
$count1 = $mod_query->num_rows;
$admin_query = $conn->query("SELECT * FROM users WHERE level='3'");
$count2 = $admin_query->num_rows;
?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="/gamerhub.css" />
<link rel="stylesheet" type="text/css" href="/bootstrap/css/bootstrap.css"/>
<meta name="viewport" content="width=device-width" />
<title>GamerHub - Staff</title>
</head>
<body align="center">
<?php include $_SERVER['DOCUMENT_ROOT']."/include/header.php"; ?>
<h3>Staff</h3>
<hr/>
<p>These are the staff members that not only help the production of Gamer Hub, but also keep the community you all know and love, safe, fun, and child friendly!</p>
<div class="inner">
<h3>Administrators</h3>
<p>These are the users that edit and add new features to the site.  they can do everything a Mod can do, along with the ability to promote/demote Mods and Admins, and delete users.</p>
<hr color="black" width="45%" align="center" />
<?php
if($count2 == "0"){
echo 'There are no Administrators.';
}else{
while($admin = $admin_query->fetch_assoc()){
echo '<a href="/user/profile.php?user='.$admin['username'].'">'.$admin['username'].'</a><br/>';
}
}
?>
</div>
<div class="inner">
<h3>Moderators</h3>
<p>These are the users that moderate the site.  They can ban/IP ban users, delete status posts, delete links, etc.</p>
<hr color="black" width="45%" align="center" />
<?php
if($count1 == "0"){
echo 'There are no Moderators.';
}else{
while($mod = $mod_query->fetch_assoc()){
echo '<a href="/user/profile.php?user='.$mod['username'].'">'.$mod['username'].'</a><br/>';
}
}
?>
</div>
</div>
<?php include $_SERVER['DOCUMENT_ROOT']."/include/footer.php"; ?>
</body>
</html>
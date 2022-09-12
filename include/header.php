</font><div class="header">
<div class="gamehead" onclick="window.location='/'"><h2>Gamer Hub...</div></h2><br /><i style="color:black;">Adds a touch to your gamer side...</i>
<br/>
</div>
</div>
<div class="inner">
<?php
if(isset($_SESSION['username'])){
echo 'Welcome, '.$_SESSION['username'].'<br/>';
echo '<a href="/user/profile.php?user='.$_SESSION['username'].'">My Profile<a> - <a href="/logout.php">Logout</a>';
}else{
echo 'Welcome, guest.<br/>';
echo '<a href="/login.php">Login</a> - <a href="/register.php">Register</a>';
}
?>
</div>
<div class="inner">
<b>Menu</b><br/>
<a href="/status">Status</a> - <a href="/news">News</a> - <a href="/newsletter">Newsletter</a> - <a href="/staff">Staff</a> - <a href="/links">Links</a>
<?php
if(isset($_SESSION['username'])) {
    if($user['level'] == "2" || $user['level'] == "3") {
        echo '<br/>';
        echo '<b>Staff Area</b><br/>';

        if($user['level'] == "2") {
            echo '<a href="/staff/mod.php">Mod Panel</a>';
        } elseif($user['level'] == "3") {
            echo '<a href="/staff/admin.php">Admin Panel</a>';
        }
        echo ' - <a href="/chat/signin.php">Staff Chat</a>';
    }
}
?>
</div>
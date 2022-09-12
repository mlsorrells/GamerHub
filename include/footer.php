<?php
echo '<font size="1">Total registered users: '.$user_num.'</font><br/>';
$newest_user = $newest_user_query->fetch_assoc();
echo '<font size="1">Newest registered user: <a href="/user/profile.php?user='.$newest_user['username'].'">'.$newest_user['username'].'</a></font><br/>';
echo '<font size="1">&copy Copyright '.date('Y').' GamerHub.  All rights reserved.</font>';
?>
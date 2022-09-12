<?php require "include/session.php"; ?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="gamerhub.css"/>
<link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css"/>
<meta name="viewport" content="width=device-width" />
<title>GamerHub - Home</title>
</head>
<body align="center">
<!-- Last edited by: qwp789. -->
<?php include "include/header.php"; ?>
<br/><br/>
<?php
while($news = mysqli_fetch_assoc($news_query)){
echo '<div class="inner">';
echo '<strong><font size="4">'.$news['subject'].'</font></strong><br/>';
echo 'By '.$news['username'].'<br/><br/>';
echo '<font size="2">'.$news['content'].'</font>';
echo '</div>';
}
?>
<?php include "include/footer.php"; ?>
</body>
</html>
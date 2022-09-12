<?php
/*
ChatBox 6.1.6
Latest update 2013-08-01 06:35
ahmad.murey@gmail.com
www.zaksdg.com
*/

require_once("./core.php");

// IIS fix
if (!isset($_SERVER['REQUEST_URI'])){
    $_SERVER['REQUEST_URI'] = $_SERVER['PHP_SELF'];
    if (isset($_SERVER['QUERY_STRING'])) { $_SERVER['REQUEST_URI'].='?'.$_SERVER['QUERY_STRING']; }
}

require dirname(__FILE__) .'/fb_sdk/facebook.php';

Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYPEER] = false;

$facebook = new Facebook(array(
  'appId'  => "",
  'secret' => "",
));

$user = $facebook->getUser();

if ($user) {
  try {
    $user_profile = $facebook->api('/me');
  } catch (FacebookApiException $e) {
    $user = null;
  }
}

if(!$user){
	header("Location: ".$facebook->getLoginUrl(array('display'=>'popup')));
	exit();
}

$_SESSION[USER_ID]=$user_profile["id"];
$_SESSION[USERNAME]=$user_profile["username"];
$_SESSION[USER_DISPLAY]=getAlias($user_profile["username"], $user_profile["name"]);
$_SESSION[USER_AUTH]=authAlgo($user_profile["username"]);
?>
<script type="text/javascript">
window.opener.initiateChatBox();
window.close();
</script>
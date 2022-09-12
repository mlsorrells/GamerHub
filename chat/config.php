<?php
/*
ChatBox 6.1.6
Latest update 2013-08-01 06:35
ahmad.murey@gmail.com
www.zaksdg.com
*/
if($_SERVER['PHP_SELF'] != "/chat/signin.php") {
	session_start();
}
date_default_timezone_set("UTC");
error_reporting(0);
ini_set("html_errors","0");

// Available rooms
$ROOMS=array(
			""=>"General Discussion",
			"fun"=>"Fun time",
			"other"=>"Other things"
		);

$roomCode=isset($_REQUEST['rm'])?trim($_REQUEST['rm']):'';
$roomName='';

if(array_key_exists($roomCode,$ROOMS)){
	$roomName=$ROOMS[$roomCode];
}else{
	$roomCode='';
	$roomName='';
}

define("ROOM_CODE",$roomCode);
define("ROOM_NAME",$roomName);

// File Paths
$pathSuffix='';
if($roomCode!='')$pathSuffix='_'.$roomCode;

define("ROOM_FILE",			"./data/room{$pathSuffix}.txt");
define("GROUP_USERS_FILE",	"./data/groupusers.txt");
define("BANNED_FILE",		"./data/bannedusers.txt");
define("KICKED_FILE",		"./data/kickedusers.txt");
define("ONLINE_FILE",		"./data/onlineusers{$pathSuffix}.txt");
define("CENSORED_FILE",		"./data/censoredwords.txt");
define("ALIAS_FILE",		"./data/alias.txt");

define("GREETINGS_FILE",	"./data/greetings.txt");
define("ANNOUNCEMENTS_FILE","./data/announcements.txt");

define("ERROR_LOG","./eLog_".date("Y_m").".log");
define("INIT_SCRIPT","");

// Chat Settings
define("COMMAND_PREFIX","\\");
define("DEFAULT_GROUP","member");
define("BOT_GROUP","bot");
define("BOT_USERNAME","bot");
define("BOT_DISPLAY_NAME","The Bot");
define("GUEST_GROUP","guest");
define("GUEST_USERNAME","guest");
define("GUEST_DISPLAY_NAME","Guest");

define("ANNOUNCEMENTS_PERIOD",10*60); // in seconds

define("ROOM_MIN_LINES",25);
define("ROOM_MAX_LINES",50);

define("SEND_GAP",2); // in seconds
define("AFK_TIME",5*60); // in seconds
define("OFFLINE_TIME",10*60); // in seconds

// Toggle Feature on/off
define("GUEST_MODE",true); // allow guests to see public messages
define("WORD_CENSOR",true); // replace censored words with ***
define("SIGNIN_GREETING",true); // display greeting messages for the signing in users
define("PUBLIC_GREETING",true); // show signing in greeting to all users
define("ANNOUNCEMENT_MODE",true); // show announcements at the specified period of time
define("ALLOW_EMOTIONS",true); // Enable/ disable emotions

// User's Sessions Variable Names
define("USER_ID","user_id");
define("USERNAME","username");
define("USER_DISPLAY","user_display");
define("GROUP","group");
define("TIMEZONE","timezone");
define("USER_AUTH","user_auth");

// Application's Sessions Variable Names
define("LAST_RECEIVED","last_received['{$roomCode}']");
define("LAST_SENT","last_sent['{$roomCode}']");
define("USERS_STATUS","users_status['{$roomCode}']");
define("LAST_ANNOUNCEMENT_TIME","last_announcement_time['{$roomCode}']");
define("ANNOUNCEMENT_COUNTER","announcement_counter['{$roomCode}']");
define("CURRENT_STATUS","current_status['{$roomCode}']");
define("LAST_MESSAGES_CHECK","last_messages_check['{$roomCode}']");
define("LAST_USERS_CHECK","last_users_check['{$roomCode}']");

// Initiate User's Sessions
if(!isset($_SESSION[USER_ID]))$_SESSION[USER_ID]=0;
if(!isset($_SESSION[USERNAME]))$_SESSION[USERNAME]="";
if(!isset($_SESSION[USER_DISPLAY]))$_SESSION[USER_DISPLAY]=$_SESSION[USERNAME];
if(!isset($_SESSION[GROUP]))$_SESSION[GROUP]="";
if(!isset($_SESSION[GROUP."_POWER"]))$_SESSION[GROUP."_POWER"]=0;
if(!isset($_SESSION[TIMEZONE]))$_SESSION[TIMEZONE]="";
if(!isset($_SESSION[USER_AUTH]))$_SESSION[USER_AUTH]="";

// Initiate Application's Sessions
if(!isset($_SESSION[LAST_RECEIVED]))$_SESSION[LAST_RECEIVED]=0;
if(!isset($_SESSION[LAST_SENT]))$_SESSION[LAST_SENT]=0;
if(!isset($_SESSION[USERS_STATUS]))$_SESSION[USERS_STATUS]="";
if(!isset($_SESSION[LAST_ANNOUNCEMENT_TIME]))$_SESSION[LAST_ANNOUNCEMENT_TIME]=time();
if(!isset($_SESSION[ANNOUNCEMENT_COUNTER]))$_SESSION[ANNOUNCEMENT_COUNTER]=0;
if(!isset($_SESSION[CURRENT_STATUS]))$_SESSION[CURRENT_STATUS]="ONLINE";
if(!isset($_SESSION[LAST_MESSAGES_CHECK]))$_SESSION[LAST_MESSAGES_CHECK]=0;
if(!isset($_SESSION[LAST_USERS_CHECK]))$_SESSION[LAST_USERS_CHECK]=0;

// Commands
$COMMANDS=array(
			"clean"=>	"<span class='note'>".COMMAND_PREFIX."clean</span><br />Clean the chat box",
			"clear"=>	"<span class='note'>".COMMAND_PREFIX."clear</span><br />Clear the room",
			"ban"=>		"<span class='note'>".COMMAND_PREFIX."ban {username} {time in minutes >=1} {cause optional}</span><br />Ban a user for a period of time",
			"unban"=>	"<span class='note'>".COMMAND_PREFIX."unban {username}</span><br />Unban a user",
			"banlist"=>	"<span class='note'>".COMMAND_PREFIX."banlist<br /></span>Display banned users",
			"kick"=>	"<span class='note'>".COMMAND_PREFIX."kick {username} {cause optional}</span><br />Kick out a user permanently",
			"unkick"=>	"<span class='note'>".COMMAND_PREFIX."unkick {username}</span><br />Unkick a user",
			"kicklist"=>"<span class='note'>".COMMAND_PREFIX."kicklist</span><br />Display kicked out users",
			"op"=>		"<span class='note'>".COMMAND_PREFIX."op {username} {group}</span><br />Assign a user to a specific operator group",
			"deop"=>	"<span class='note'>".COMMAND_PREFIX."deop {username}</span><br />Remove a user from his group and assign him to the default group",
			"users"=>	"<span class='note'>".COMMAND_PREFIX."users {group|*}</span><br />Display users in specific|all group(s)",
			"groups"=>	"<span class='note'>".COMMAND_PREFIX."groups</span><br />Display available groups with assigned permissions",
			"rooms"=>	"<span class='note'>".COMMAND_PREFIX."rooms</span><br />Display available rooms",
			"cr"=>		"<span class='note'>".COMMAND_PREFIX."cr {room code}</span><br />Change room",
			"alias"=>	"<span class='note'>".COMMAND_PREFIX."alias {display name|\"\"}</span><br />change your display name, use &quot;&quot; to remove",
			"help"=>	"<span class='note'>".COMMAND_PREFIX."help</span><br />Display available commands available for the users"
			);

// Permissions: the group order gives it the power (sa can ban admin but not the vice versa)
$PERMISSIONS=array(
				"sa"=>		array("clean","clear","ban","unban","banlist","kick","unkick","kicklist","op","deop","users","groups","rooms","cr","alias","help"),
				"admin"=>	array("clean","clear","ban","unban","banlist","kick","unkick","kicklist","op","deop","users","groups","rooms","cr","alias","help"),
				"mod"=>		array("clean","clear","ban","banlist","rooms","cr","alias","help"),
				DEFAULT_GROUP=>array("clean","rooms","cr","alias","help")
			);
?>
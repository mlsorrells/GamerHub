<?php
/*
ChatBox 6.1.6
Latest update 2013-08-01 06:35
ahmad.murey@gmail.com
www.zaksdg.com
*/

// check if magic quotes are enabled in php directives and strip the slashes if it's so
if(get_magic_quotes_gpc())$_REQUEST=array_map("stripslashes",$_REQUEST);

require_once ("./core.php");

/*
Requests:
o = operation
rm = room code
u = username
ud= user display name
z = timezone
m = message
to= to users
r: isReloading

Responses
nm= new messages
ol= online users
sc= server command
*/

$oper=isset($_REQUEST["o"])?trim($_REQUEST["o"]):"";

$RESP=array(
			"nm"=>array(),
			"ol"=>"",
			"sc"=>array()
		);

switch($oper){
	case "init":
		require_once ("./client_config.php");
		$isReloading=isset($_REQUEST["r"])?$_REQUEST["r"]:false;
		
		if(INIT_SCRIPT!="" && !$isReloading)require_once(INIT_SCRIPT);
		
		if($_SESSION[USER_AUTH]=="" && $_SESSION[USERNAME]=="" && GUEST_MODE)$_SESSION[USER_AUTH]=GUEST_USERNAME;
		
		// Return emotions. client configs and current user main properties
		$RESP=array("sc"=>array("init",$EMOTIONS,$CONFIG,GUEST_MODE,array(
															"id"=>$_SESSION[USER_ID],
															"u"=>$_SESSION[USERNAME],
															"ud"=>$_SESSION[USER_DISPLAY],
															"a"=>$_SESSION[USER_AUTH]
														))
				);
	break;
	
	case "signin":
		// Requests: id=user id, u=username, ud=user display name, z=timezone, a=user authentication key
		$_SESSION[USER_ID]=isset($_REQUEST["id"])?intval($_REQUEST["id"]):0;
		
		$user=isset($_REQUEST["u"])?trim($_REQUEST["u"]):"";
		
		if($user=="" || $user==GUEST_USERNAME){
			if(GUEST_MODE){
				$user=GUEST_USERNAME;
				$_REQUEST["ud"]=GUEST_DISPLAY_NAME;
			}else{
				break;
			}
		}
		
		//hack attempt
		if($user!=GUEST_USERNAME && $_SESSION[USER_AUTH]==GUEST_USERNAME){
			postMessage(0,BOT_USERNAME,BOT_DISPLAY_NAME,BOT_GROUP,$_SESSION[USERNAME],"Good try BTW, but you just can't do this ;)");
			break;
		}
		
		if(!authenticated($user)){
			$RESP["sc"]=array("signin");
			break;
		}
		
		$group=getGroup($user);
		// The group will be changed when user is signing in
		$isAlreadySignedIn=($_SESSION[GROUP]==$group[0]);
		
		$_SESSION[USERNAME]=$user;
		$_SESSION[GROUP]=$group[0];
		$_SESSION[GROUP."_POWER"]=$group[1];
	
		if(isset($_REQUEST["ud"])){
			$ud=str_replace(array("\t","\n"),array("&harr;","&crarr;"),trim($_REQUEST["ud"]));
			if($ud=="" || ($user!=GUEST_USERNAME && $ud==GUEST_DISPLAY_NAME))$ud=$user;
			$_SESSION[USER_DISPLAY]=$ud;
		}
		
		$tz=isset($_REQUEST["z"])?$_REQUEST["z"]:0;
		$_SESSION[TIMEZONE]=intval($tz)." hours ".(($tz-intval($tz))*60)." minutes";
		
		$k=isKicked();
		if($k!==false){
			$msg=$k;
			break;
		}
		
		$_SESSION[LAST_RECEIVED]=0;
		$_SESSION[LAST_SENT]=microtime(true);
		$_SESSION[LAST_ANNOUNCEMENT_TIME]=time();
		$_SESSION[USERS_STATUS]=array();
		$_SESSION[LAST_MESSAGES_CHECK]=0;
		$_SESSION[LAST_USERS_CHECK]=0;
		$_SESSION[CURRENT_STATUS]="ONLINE";
		
		updateUser();
		if(SIGNIN_GREETING && !$isAlreadySignedIn){
			postMessage(0,BOT_USERNAME,BOT_DISPLAY_NAME,BOT_GROUP,PUBLIC_GREETING?"":$_SESSION[USERNAME],"{$_SESSION[USERNAME]} / {$_SESSION[USER_DISPLAY]} joined<br /><span class='greeting'>".getGreeting()."</span>");
		}else{
			getNewMessages();
		}
		$RESP["sc"]=array("logged",array(
										"id"=>$_SESSION[USER_ID],
										"u"=>$_SESSION[USERNAME],
										"g"=>$_SESSION[GROUP],
										"ud"=>$_SESSION[USER_DISPLAY]
									));
	break;

	case "signout":
		if($_SESSION[USERNAME]!=""){
			postMessage(0,BOT_USERNAME,BOT_DISPLAY_NAME,BOT_GROUP,"","{$_SESSION[USERNAME]} / {$_SESSION[USER_DISPLAY]} left");
			// Clear user group, so the updateUser function will delete it from the online users file
			$_SESSION[GROUP]="";
			updateUser();
			unset($_SESSION[USER_ID],$_SESSION[USERNAME],$_SESSION[USER_DISPLAY],$_SESSION[GROUP],$_SESSION[GROUP."_POWER"],$_SESSION[TIMEZONE],$_SESSION[USER_AUTH],$_SESSION[USERS_STATUS], $_SESSION[CURRENT_STATUS]);
		}

		$RESP["sc"]=array("reload",GUEST_MODE);
	break;
	
	case "send":
		// Requests: m=message, to=to users
		
		// Not signed in or guest
		if($_SESSION[USERNAME]=="" || $_SESSION[GROUP]==GUEST_GROUP){
			if(GUEST_MODE)postMessage(0,BOT_USERNAME,BOT_DISPLAY_NAME,BOT_GROUP,$_SESSION[USERNAME],"You have to sign in first");
			break;
		}
		
		// Sending gap violation
		if(microtime(true)-$_SESSION[LAST_SENT]<SEND_GAP){
			// Format: sent time -> user id -> username -> user display name -> group -> is private -> message
			$RESP["nm"][]=ltime("h:i a")."\t0\t".BOT_USERNAME."\t".BOT_DISPLAY_NAME."\t".BOT_GROUP."\t". true ."\tYou're sending a lot of messages in a short period of time!";
			break;
		}

		$k=isKicked();
		if($k!==false){
			$msg=$k;
			break;
		}
		
		$b=isBanned();
		if($b!==false){
			postMessage(0,BOT_USERNAME,BOT_DISPLAY_NAME,BOT_GROUP,$_SESSION[USERNAME],$b);
			break;
		}
		
		$msg=trim(htmlspecialchars($_REQUEST["m"]));
		$msg=str_replace(array("\t","\n"),array("&harr;","&crarr;"),$msg);

		if($_SESSION[GROUP]!=GUEST_GROUP && $msg!="" && substr($msg,0,strlen(COMMAND_PREFIX))==COMMAND_PREFIX){
			$strCom=substr($msg,strlen(COMMAND_PREFIX));
			$com=trim(substr($strCom,0,strpos($strCom," ",strlen(COMMAND_PREFIX))));
			if($com=="")$com=$strCom;
			$com=strtolower($com);

			if(array_key_exists($com,$COMMANDS)){
				doCommand($com,$msg);
				break;
			}
		}
		// we update user last sent message time at this point to detect any changes in the group made using op / deop commands before posting the message
		updateUser();
		postMessage($_SESSION[USER_ID],$_SESSION[USERNAME],$_SESSION[USER_DISPLAY],$_SESSION[GROUP],trim($_REQUEST["to"]),$msg);
	break;
	
	case "check":
		// Not signed
		if($_SESSION[USERNAME]=="")break;
		
		$k=isKicked();
		if($k!==false){
			$msg=$k;
			break;
		}
		
		if(getOnlineUsers())getNewMessages();

		if(ANNOUNCEMENT_MODE!="")getAnnouncements();
	break;
}

header("Content-Type: application/json");
echo json_encode($RESP);
?>
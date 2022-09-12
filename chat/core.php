<?php
/*
ChatBox 6.1.6
Latest update 2013-08-01 06:35
ahmad.murey@gmail.com
www.zaksdg.com
*/

require_once(dirname(__FILE__)."/config.php");

$CENSORED_WORDS_CACHE="";
set_error_handler("error_function",E_ALL);

function error_function($level,$msg,$file,$line){
	global $RESP;

	error_log(date("Y-m-d H:i:s")."\tLV:$level\tM:$msg\tF:$file\t L:$line\n", 3, ERROR_LOG);
	$RESP["sc"]=array("error","Oops, an Error occured, please check error log [".ERROR_LOG."]");
	echo json_encode($RESP);
	exit();
}

function ltime($format="Y-m-d H:i", $time=0){
	if($time==0)$time=time();
	$nd=new DateTime(date("Y-m-d H:i",$time));
	if(trim(TIMEZONE)!="")$nd->modify($_SESSION[TIMEZONE]);
	return $nd->format($format);
}

function authAlgo($input){
	return md5($input);
}

function authenticated($username){
	if(trim($username)=="")return false;
	if($_SESSION[USER_AUTH]==GUEST_USERNAME)return true;
	
	// you can change this authentication check according to the key created by the signin procedure
	return ($_SESSION[USER_AUTH]==authAlgo($username));
}

function censoredWords($str){
	global $CENSORED_WORDS_CACHE;

	if(!WORD_CENSOR)return $str;
	if($CENSORED_WORDS_CACHE=="")$CENSORED_WORDS_CACHE=file(CENSORED_FILE,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	for ($i=0;$i<count($CENSORED_WORDS_CACHE);$i++){
		$word=$CENSORED_WORDS_CACHE[$i];
		$fLetter=substr($word,0,1);
		$lLetter=substr($word,-1);
		$word=substr($word,1,strlen($word)-2);

		$str=preg_replace("/(^|\W|\d)$fLetter{$word}$lLetter(\W|\d|$)/i","$1{$fLetter}".str_repeat("*",strlen($word))."{$lLetter}$2",$str);
	}
	return $str;
}

function getAlias($user, $default=null){
	$alias=file(ALIAS_FILE,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	for($i=0;$i<count($alias);$i++){
		$ainfo=explode("\t",$alias[$i]);
		if($ainfo[0]==$user){
			return $ainfo[1];
		}
	}
	return ($default==null)?$user:$default;
}

function getGroup($username=null){
	global $PERMISSIONS;
	
	if($username=="")$username=$_SESSION[USERNAME];
	
	if($username==GUEST_USERNAME)return array(GUEST_GROUP,0);

	$group=DEFAULT_GROUP;
	
	$gusers=file(GROUP_USERS_FILE,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	foreach($gusers as $line){
		$guInfo=explode("\t",$line);
		// Format: group -> username -> set by -> date
		if($guInfo[1]==$username){
			$group=$guInfo[0];
			break;
		}
	}

	$power=array_search($group,array_keys($PERMISSIONS));
	$power=($power===false)?0:count($PERMISSIONS)-$power;
	return array($group,$power);
}

function groupPower($user){
	$group=getGroup($user);
	return $group[1];
}

function postMessage($id,$from,$fromDisp,$group,$to,$msg){
	if($msg=="" || $from=="")return;
	
	global $RESP;
	
	$RESP["nm"]=array();
	$room=null;

	// The client resumes messaging from sleep mode, so let's wake up the client
	if($_SESSION[CURRENT_STATUS]=="SLEEP")$RESP["sc"]=array("wakeup");
	
	if($_SESSION[CURRENT_STATUS]!="ONLINE")$_SESSION[CURRENT_STATUS]="ONLINE";
	
	$_SESSION[LAST_SENT]=microtime(true);
	
	$mTime=filemtime(ROOM_FILE);
	if($mTime>$_SESSION[LAST_MESSAGES_CHECK]){
		$_SESSION[LAST_MESSAGES_CHECK]=$mTime;
		$room=file(ROOM_FILE,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	}
	
	// Format: sent time -> user id -> username -> user display name -> group - to usernames -> message
	$data=$_SESSION[LAST_SENT]."\t$id\t$from\t$fromDisp\t$group\t$to\t$msg";
	$room[]=$data;
	
	// No need to save the message to the room file if it refers to the poster himself
	// It happens when sending a private message to yourself or executing some commands
	if($_SESSION[USERNAME]!=$to){
		// Clean up the room id exceeded ROOM_MAX_LINES, and shrink it down to ROOM_MIN_LINES
		if(count($room)>ROOM_MAX_LINES){
			$room=array_slice($room,-ROOM_MIN_LINES);
			file_put_contents(ROOM_FILE,implode("\n",$room)."\n",LOCK_EX);
		}else{
			file_put_contents(ROOM_FILE,"$data\n",FILE_APPEND | LOCK_EX);
		}
	}

	getNewMessages($room);
}

function getNewMessages($room=null){
	global $RESP;
		
	if(microtime(true)-$_SESSION[LAST_SENT]>OFFLINE_TIME)return false;

	$mTime=filemtime(ROOM_FILE);
	if($room==null && $mTime>$_SESSION[LAST_MESSAGES_CHECK]){
		$_SESSION[LAST_MESSAGES_CHECK]=$mTime;
		$room=file(ROOM_FILE,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	}
	
	if($room==null) return;
	
	foreach($room as $line){
		$mArgs=explode("\t",$line);
		if(count($mArgs)<7)continue;
		$to=strtolower($mArgs[5]);
		$isPrivate=in_array(strtolower($_SESSION[USERNAME]),explode(",",$to));
		if($isPrivate || strtolower($_SESSION[USERNAME])==strtolower($mArgs[2]) || $to==""){

			if($mArgs[0]>$_SESSION[LAST_RECEIVED]){
				// Format: sent time -> user id -> username -> user display name -> group -> is private -> message
				$RESP["nm"][]=ltime("h:i a",intval($mArgs[0]))."\t{$mArgs[1]}\t{$mArgs[2]}\t{$mArgs[3]}\t{$mArgs[4]}\t$isPrivate\t".censoredWords($mArgs[6]);
				$_SESSION[LAST_RECEIVED]=$mArgs[0];
			}
		}
	}
}

function updateUser(){
	global $PERMISSIONS;
	
	$onlineusers=file(ONLINE_FILE,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	
	$isExists=false;
	for($i=0;$i<count($onlineusers);$i++){
		$uinfo=explode("\t",$onlineusers[$i]);

		if(count($uinfo)<5)continue;
		if(strtolower($uinfo[1])==strtolower($_SESSION[USERNAME])){
			// When group="" the user is signing out
			if($_SESSION[GROUP]==""){
				unset($onlineusers[$i]);
			}else{
				if($_SESSION[GROUP]!=$uinfo[3]){
					$_SESSION[GROUP]=$uinfo[3];
					$power=array_search($uinfo[3],array_keys($PERMISSIONS));
					$power=($power===false)?0:count($PERMISSIONS)-$power;
					$_SESSION[GROUP."_POWER"]=$power;
				}

				$onlineusers[$i]="{$_SESSION[USER_ID]}\t{$_SESSION[USERNAME]}\t{$_SESSION[USER_DISPLAY]}\t{$_SESSION[GROUP]}\t".microtime(true);
			}
			$isExists=true;
		}

		// update user group after executing op, deop commands
		if(isset($_REQUEST['updategroup']) && strtolower($uinfo[1])==$_REQUEST['updategroup']['user']){
			$onlineusers[$i]="{$uinfo[0]}\t{$uinfo[1]}\t{$uinfo[2]}\t{$_REQUEST['updategroup']['group']}\t{$uinfo[4]}";
		}
	}
	if(!$isExists)$onlineusers[]="{$_SESSION[USER_ID]}\t{$_SESSION[USERNAME]}\t{$_SESSION[USER_DISPLAY]}\t{$_SESSION[GROUP]}\t".microtime(true);

	// Format: user id -> username -> user display name -> group -> last post time
	file_put_contents(ONLINE_FILE,implode("\n",$onlineusers),LOCK_EX);

	if($_SESSION[GROUP]!="")getOnlineUsers($onlineusers);
}


function compGroup($a,$b){
	global $PERMISSIONS;
	
	// find group indexes
	$aArr=explode("\t", $a);
	$aIndex=array_search($aArr[3],array_keys($PERMISSIONS));
	if($aIndex===false)$aIndex=999;
	
	$bArr=explode("\t", $b);
	$bIndex=array_search($bArr[3],array_keys($PERMISSIONS));
	if($bIndex===false)$bIndex=999;
	
	// sort by group power
	return ($aIndex<$bIndex)?-1:1;
}

function getOnlineUsers($onlineusers=null){
	global $RESP;
	
	$awayTime=microtime(true)-$_SESSION[LAST_SENT];
	$mTime=filemtime(ONLINE_FILE);

	if($onlineusers==null && (($mTime>$_SESSION[LAST_USERS_CHECK]) || ($_SESSION[CURRENT_STATUS]!="AWAY" && $awayTime>=AFK_TIME) || ($_SESSION[CURRENT_STATUS]!="SLEEP" && $awayTime>=OFFLINE_TIME))){
		$_SESSION[LAST_USERS_CHECK]=$mTime;
		$onlineusers=file(ONLINE_FILE,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	}

	$doTouch=false;
	// Check if current user reaches AFK oeriod, if so, send (away) command to change server checking interval
	if($_SESSION[CURRENT_STATUS]!="AWAY" && $awayTime>=AFK_TIME){ 
		$RESP["sc"]=array("away");
		$_SESSION[CURRENT_STATUS]="AWAY";
		$doTouch=true;
	}
	
	// Check if current user reaches OFFLINE period, if so, send (sleep) command to stop checking the server for updates
	if($_SESSION[CURRENT_STATUS]!="SLEEP" && $awayTime>=OFFLINE_TIME ){
		$RESP["sc"]=array("sleep");
		$_SESSION[CURRENT_STATUS]="SLEEP";
		$doTouch=true;
	}

	$doSave=false;
	$newMessageSent=false;
	$usersStatus=array();
	
	$RESP["ol"]=array();

	if($onlineusers!=null){
		for($i=0;$i<count($onlineusers);$i++){
			$uinfo=explode("\t",$onlineusers[$i]);
			if(count($uinfo)<5 || $uinfo[3]=="")continue;
			
			// Remove the user from the online list if the user stopped messaging for OFFLINE_TIME seconds.
			if(microtime(true)-$uinfo[4]>=OFFLINE_TIME){
				unset($onlineusers[$i]);
				$doSave=true;
				continue;
			}
			// Check if there is a new message to make the client decide whether to read the new messages or not
			if($uinfo[4]>$_SESSION[LAST_RECEIVED])$newMessageSent=true;
			
			// Check if the user is away from the keyboard
			$status=(microtime(true)-$uinfo[4]>=AFK_TIME)?"away":"online";
				
			$RESP["ol"][]="{$uinfo[0]}\t{$uinfo[1]}\t{$uinfo[2]}\t{$uinfo[3]}\t$status";
			// Get online users status
			// Format: username -> display name -> group -> status
			$usersStatus[]="{$uinfo[1]}{$uinfo[2]}{$uinfo[3]}$status";
		}

		// Detect users changes (in/out users, group changes, user status, display name)
		// Compare new status with the previously saved one, return "" if no changes detected
		$us=md5(implode($usersStatus));

		// if no changes detected send "" for online users list
		if($_SESSION[USERS_STATUS]==$us)$RESP["ol"]="";
		$_SESSION[USERS_STATUS]=$us;
		
		if($doSave){
			file_put_contents(ONLINE_FILE,implode("\n",$onlineusers),LOCK_EX);
		}else{
			// this will trigger getOnlineUsers() to reload online users list in case of current user is going away at this stage
			if($doTouch){
				touch(ONLINE_FILE);
				if($RESP["ol"]=="")$RESP["ol"]=array();
			}
		}
		
		// sort users by group power
		if(is_array($RESP["ol"]))usort($RESP["ol"],"compGroup");
	}

	// if no changes detected send "" for online users list
	if($onlineusers==null && !($doTouch || $doSave))$RESP["ol"]="";
	
	return $newMessageSent;
}

function isBanned(){
	$bannedusers=file(BANNED_FILE,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$msg="";
	$doSave=false;
	for($i=0;$i<count($bannedusers);$i++){
		$binfo=explode("\t",$bannedusers[$i]);
		if(time()>=$binfo[1]){
			// Remove users from banned list if ban is expired	
			unset($bannedusers[$i]);
			$doSave=true;
			continue;
		}
		if($binfo[0]==$_SESSION[USERNAME])$msg="You are banned for ".round(($binfo[1]-time())/60,1)." Minute(s), Reason: {$binfo[3]}";
	}

	if($doSave)file_put_contents(BANNED_FILE,implode("\n",$bannedusers),LOCK_EX);

	return ($msg=="")?false:$msg;
}

function isKicked(){
	global $RESP;
	
	$kickedusers=file(KICKED_FILE,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	foreach($kickedusers as $line){
		$kinfo=explode("\t",$line);
		if($kinfo[0]==$_SESSION[USERNAME]){
			$RESP["sc"]=array("kicked","You are kicked out, Reason: {$kinfo[3]}");
			return "{$kinfo[0]} is kicked out, Reason: {$kinfo[3]}";
		}
	}
	return false;
}

function getGreeting(){
	if(GREETINGS_FILE=="" || !file_exists(GREETINGS_FILE))return false;

	$greetings=file(GREETINGS_FILE,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	$grt="";
	if(count($greetings)>0)$grt=$greetings[mt_rand(0,count($greetings)-1)];
	$grt=str_replace(array("{username}","{user_display}","{group}","{room}"),array($_SESSION[USERNAME],$_SESSION[USER_DISPLAY],$_SESSION[GROUP],ROOM_NAME),$grt);
	return $grt;
}

function getAnnouncements(){
	if(time()-$_SESSION[LAST_ANNOUNCEMENT_TIME]<ANNOUNCEMENTS_PERIOD)return false;
	
	if(ANNOUNCEMENTS_FILE=="" || !file_exists(ANNOUNCEMENTS_FILE))return false;

	$announcement=file(ANNOUNCEMENTS_FILE,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	if(count($announcement)==0)return false;
	
	global $RESP;
	
	
	$ann="";
	if($_SESSION[ANNOUNCEMENT_COUNTER]>=count($announcement))$_SESSION[ANNOUNCEMENT_COUNTER]=0;
	$ann=$announcement[$_SESSION[ANNOUNCEMENT_COUNTER]];
	$ann=str_replace(array("{username}","{user_display}","{group}","{room}"),array($_SESSION[USERNAME],$_SESSION[USER_DISPLAY],$_SESSION[GROUP],ROOM_NAME),$ann);
	$_SESSION[ANNOUNCEMENT_COUNTER]++;
	$_SESSION[LAST_ANNOUNCEMENT_TIME]=time();
	
	// Format: sent time -> user id -> username -> user display name -> group -> is private -> message
	if($ann!="")$RESP["nm"][]=ltime("h:i a")."\t0\t".BOT_USERNAME."\t".BOT_DISPLAY_NAME." - Announcement\t".BOT_GROUP."\t".true."\t<span class='announcement'>".censoredWords($ann)."</span>";
}

function doCommand($com,$msg){
	global $RESP,$COMMANDS,$PERMISSIONS;
	
	$user=$_SESSION[USERNAME];
	
	$MSGARR=array(
				"NOT_ALLOWED"=>"The <span class='note'>$com</span> Command is not allowed",
				"MISSING_ARGS"=>"<span class='note'><b>Missing arguments</b></span><br />Syntax: {$COMMANDS[$com]}",
				"SET_BY_MORE_POWER"=>"<span class='note'>This user is handled by a more powerful user</span>",
				"NO_POWER"=>"<span class='note'>You don't have the power to <b>$com</b> this user</span>",
				"CANT_COM_YOURSELF"=>"<span class='note'>You can't <b>$com</b> yourself</span>",
				"CANT_COM_GUEST"=>"<span class='note'>You can't <b>$com</b> the guest</span>"
			);
	
	if(array_key_exists($_SESSION[GROUP],$PERMISSIONS)){
		if(!in_array($com,$PERMISSIONS[$_SESSION[GROUP]])){
			$msg=$MSGARR["NOT_ALLOWED"];
			$_REQUEST['to']=$user;
			$com="";
		}
	}else{
		$msg=$MSGARR["NOT_ALLOWED"];
		$_REQUEST['to']=$user;
		$com="";
	}

	if($com!="")require_once("./commands.php");
	updateUser();
	postMessage(0,BOT_USERNAME,BOT_DISPLAY_NAME,BOT_GROUP,trim($_REQUEST['to']),$msg);
}
?>
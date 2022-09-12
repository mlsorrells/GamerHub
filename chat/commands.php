<?php
/*
ChatBox 6.1.6
Latest update 2013-08-01 06:35
ahmad.murey@gmail.com
www.zaksdg.com
*/
$strCom=substr($msg,strlen(COMMAND_PREFIX));

switch($com){
	case "clear":
		// Syntax: \clear
		file_put_contents(ROOM_FILE,"",LOCK_EX);
		$msg="<span class='note'>Room is Cleared by $user</span>";
		$_REQUEST['to']="";
	break;
	
	case "clean":
		// Syntax: \clean
		$msg="";
		$_REQUEST['to']=$user;
		$RESP['sc']=array("clean");
	break;
	
	case "ban":
		// Syntax: \ban {username} {time in minutes >=1} {cause optional}
		$args=explode(" ",preg_replace("/ +/"," ",$strCom),4);
		if(!isset($args[1],$args[2])){
			$msg=$MSGARR["MISSING_ARGS"];
			$_REQUEST['to']=$user;
			break;
		}
		
		if($user==$args[1] || $args[1]==GUEST_USERNAME){
			$msg=$MSGARR["CANT_COM_".($args[1]==GUEST_USERNAME?"GUEST":"YOURSELF")];
			$_REQUEST['to']=$user;
			break;
		}
		
		if(!isset($args[3]))$args[3]="Unkown reason";
		
		$bannedusers=file(BANNED_FILE,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		for($i=0;$i<count($bannedusers);$i++){
			$binfo=explode("\t",$bannedusers[$i]);
			if($binfo[0]==$args[1]){
				// check if already banned by a more powerfull user
				if($_SESSION[GROUP."_POWER"]<groupPower($binfo[2])){
					$msg=$MSGARR["SET_BY_MORE_POWER"];
					$_REQUEST['to']=$user;
					break 2;
				}
				
				unset($bannedusers[$i]);
				break;
			}
		}
		
		$args[2]=intval($args[2]);
		if($args[2]<1){
			$msg="<span class='note'>Bad ban time value</span><br />Syntax: {$COMMANDS[$com]}";
			$_REQUEST['to']=$user;
			break;
		}
		
		// can ban only users with less power
		if($_SESSION[GROUP."_POWER"]<=groupPower($args[1])){
			$msg=$MSGARR["NO_POWER"];
			$_REQUEST['to']=$user;
			break;
		}
		
		// Format: username -> ban expire time -> set by username -> cause
		// Calculate ban expire time
		$bexp=time()+$args[2]*60;
		$bannedusers[]="{$args[1]}\t$bexp\t$user\t{$args[3]}";
		file_put_contents(BANNED_FILE,implode("\n",$bannedusers),LOCK_EX);
		$msg="{$args[1]} is banned for {$args[2]} minutes ({$args[3]})";
		
		$_REQUEST['to']="$user,{$args[1]}";
	break;
	
	case "unban":
		// Syntax: \unban {username}
		$args=explode(" ",preg_replace("/ +/"," ",$strCom),3);
		if(!isset($args[1])){
			$msg=$MSGARR["MISSING_ARGS"];
			$_REQUEST['to']=$user;
			break;
		}
		
		if($user==$args[1] || $args[1]==GUEST_USERNAME){
			$msg=$MSGARR["CANT_COM_".($args[1]==GUEST_USERNAME?"GUEST":"YOURSELF")];
			$_REQUEST['to']=$user;
			break;
		}
		
		$bannedusers=file(BANNED_FILE,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		$isBanned=false;
		$setBy="";
		for($i=0;$i<count($bannedusers);$i++){
			$binfo=explode("\t",$bannedusers[$i]);
			if($binfo[0]==$args[1]){
				$setBy=$binfo[2];
				unset($bannedusers[$i]);
				$isBanned=true;
				break;
			}
		}
		
		if($isBanned){
			// can unban only users set by users with similer/less power
			if($_SESSION[GROUP."_POWER"]<groupPower($setBy)){
				$msg=$MSGARR["SET_BY_MORE_POWER"];
				$_REQUEST['to']=$user;
				break;
			}
			
			file_put_contents(BANNED_FILE,implode("\n",$bannedusers),LOCK_EX);
			$msg="{$args[1]} is unbanned";
			$_REQUEST['to']="$user,{$args[1]}";
		}else{
			$msg="<b>{$args[1]}</b> is not in the banned users list";
			$_REQUEST['to']=$user;
		}
	break;
	
	case "banlist":
		// Syntax: \banlist
		$bannedusers=file(BANNED_FILE,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		$msg="No Banned users were found";
		if(count($bannedusers)>0){
			$msg="Banned Users:<br /><table class='banlist'><tr><th>User</th><th>Time</th><th>Issuer</th><th>Reason</th></tr>";
			foreach($bannedusers as $line){
				$binfo=explode("\t",$line);
				$msg.="<tr><td>{$binfo[0]}</td><td>".round(($binfo[1]-time())/60,1)."</td><td>{$binfo[2]}</td><td>{$binfo[3]}</td></tr>";
			}
			
			$msg.="</table>";
		}
		
		$_REQUEST['to']=$user;
	break;
	
	case "kick":
		// Syntax: \kick {username} {cause optional}
		$args=explode(" ",preg_replace("/ +/"," ",$strCom),3);
		if(!isset($args[1])){
			$msg=$MSGARR["MISSING_ARGS"];
			$_REQUEST['to']=$user;
			break;
		}
		
		if($user==$args[1] || $args[1]==GUEST_USERNAME){
			$msg=$MSGARR["CANT_COM_".($args[1]==GUEST_USERNAME?"GUEST":"YOURSELF")];
			$_REQUEST['to']=$user;
			break;
		}
		
		if(!isset($args[2]))$args[2]="Unkown reason";
		
		$kickedusers=file(KICKED_FILE,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		for($i=0;$i<count($kickedusers);$i++){
			$kinfo=explode("\t",$kickedusers[$i]);
			if($kinfo[0]==$args[1]){
				// check if already kicked by a more powerfull user
				if($_SESSION[GROUP."_POWER"]<groupPower($kinfo[2])){
					$msg=$MSGARR["SET_BY_MORE_POWER"];
					$_REQUEST['to']=$user;
					break 2;
				}
				
				unset($kickedusers[$i]);
				break;
			}
		}
		
		// can kick only users with less power
		if($_SESSION[GROUP."_POWER"]<=groupPower($args[1])){
			$msg=$MSGARR["NO_POWER"];
			$_REQUEST['to']=$user;
			break;
		}
		
		// Format: username -> time -> set by username -> cause
		$kickedusers[]="{$args[1]}\t".time()."\t$user\t{$args[2]}";
		file_put_contents(KICKED_FILE,implode("\n",$kickedusers),LOCK_EX);
		$msg="{$args[1]} is kicked out ({$args[2]})";
		
		$_REQUEST['to']="$user,{$args[1]}";
	break;
	
	case "unkick":
		// Syntax: \unkick {username}
		$args=explode(" ",preg_replace("/ +/"," ",$strCom),3);
		if(!isset($args[1])){
			$msg=$MSGARR["MISSING_ARGS"];
			$_REQUEST['to']=$user;
			break;
		}
		
		if($user==$args[1] || $args[1]==GUEST_USERNAME){
			$msg=$MSGARR["CANT_COM_".($args[1]==GUEST_USERNAME?"GUEST":"YOURSELF")];
			$_REQUEST['to']=$user;
			break;
		}
		
		$kickedusers=file(KICKED_FILE,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		$isKicked=false;
		$setBy="";
		for($i=0;$i<count($kickedusers);$i++){
			$kinfo=explode("\t",$kickedusers[$i]);
			if($kinfo[0]==$args[1]){
				$setBy=$kinfo[2];
				unset($kickedusers[$i]);
				$isKicked=true;
				break;
			}
		}
		if($isKicked){
			// can unkick only users set by users with similer/less power
			if($_SESSION[GROUP."_POWER"]<groupPower($setBy)){
				$msg=$MSGARR["SET_BY_MORE_POWER"];
				$_REQUEST['to']=$user;
				break;
			}
			
			file_put_contents(KICKED_FILE,implode("\n",$kickedusers),LOCK_EX);
			$msg="{$args[1]} is unkicked";
			$_REQUEST['to']="$user,{$args[1]}";
		}else{
			$msg="<span class='note'><b>{$args[1]}</b> is not in the kicked users list</span>";
			$_REQUEST['to']=$user;
		}
	break;
	
	case "kicklist":
		// Syntax: \kciklist
		$kickedusers=file(KICKED_FILE,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		$msg="No Kicked users were found";
		if(count($kickedusers)>0){
			$msg="Kicked Users:<br /><table class='kicklist'><tr><th>User</th><th>Date</th><th>Issuer</th><th>Reason</th></tr>";
			foreach($kickedusers as $line){
				$kinfo=explode("\t",$line);
				$msg.="<tr><td>{$kinfo[0]}</td><td>".ltime("Y-m-d h:i a",intval($kinfo[1]))."</td><td>{$kinfo[2]}</td><td>{$kinfo[3]}</td></tr>";
			}
			$msg.="</table>";
		}
		
		$_REQUEST['to']=$user;
	break;

	case "op":
		// Syntax: \op {username} {group}
		$args=explode(" ",preg_replace("/ +/"," ",$strCom),3);
		if(!isset($args[1],$args[2])){
			$msg=$MSGARR["MISSING_ARGS"];
			$_REQUEST['to']=$user;
			break;
		}
		
		if($user==$args[1] || $args[1]==GUEST_USERNAME){
			$msg=$MSGARR["CANT_COM_".($args[1]==GUEST_USERNAME?"GUEST":"YOURSELF")];
			$_REQUEST['to']=$user;
			break;
		}
		
		if(!array_key_exists($args[2],$PERMISSIONS)){
			$msg="<span class='note'>Unkown group name (<b>{$args[2]}</b>)";
			$_REQUEST['to']=$user;
			break;
		}
		
		// can only add users to groups with less power
		$power=array_search($args[2],array_keys($PERMISSIONS));
		$power=($power===false)?0:count($PERMISSIONS)-$power;
		if($_SESSION[GROUP."_POWER"]<=$power){
			$msg=$MSGARR["NO_POWER"]." / group";
			$_REQUEST['to']=$user;
			break;
		}
		
		// check if already op'ed by a more powerfull user
		if($_SESSION[GROUP."_POWER"]<=groupPower($args[1])){
			$msg=$MSGARR["NO_POWER"];
			$_REQUEST['to']=$user;
			break;
		}

		$groupusers=file(GROUP_USERS_FILE,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		for($i=0;$i<count($groupusers);$i++){
			$uinfo=explode("\t",$groupusers[$i]);
			if($uinfo[1]==$args[1]){
				// check if already op'ed by a more powerfull user
				if($_SESSION[GROUP."_POWER"]<groupPower($uinfo[2])){
					$msg=$MSGARR["SET_BY_MORE_POWER"];
					$_REQUEST['to']=$user;
					break 2;
				}
				
				unset($groupusers[$i]);
				break;
			}
		}
		
		// Format: group -> username -> set by username -> date
		$groupusers[]="{$args[2]}\t{$args[1]}\t$user\t".ltime("Y-m-d h:i a");
		file_put_contents(GROUP_USERS_FILE,implode("\n",$groupusers),LOCK_EX);
		$msg="<b>{$args[1]}</b> joined the <b>{$args[2]}</b> group<br />";
		
		$_REQUEST['to']="$user,{$args[1]}";
		$_REQUEST['updategroup']=array("user"=>$args[1],"group"=>$args[2]);

	break;
	
	case "deop":
		// Syntax: \deop {username}
		$args=explode(" ",preg_replace("/ +/"," ",$strCom),3);
		if(!isset($args[1])){
			$msg=$MSGARR["MISSING_ARGS"];
			$_REQUEST['to']=$user;
			break;
		}
		
		if($user==$args[1] || $args[1]==GUEST_USERNAME){
			$msg=$MSGARR["CANT_COM_".($args[1]==GUEST_USERNAME?"GUEST":"YOURSELF")];
			$_REQUEST['to']=$user;
			break;
		}
		
		$groupusers=file(GROUP_USERS_FILE,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		$grp="";
		for($i=0;$i<count($groupusers);$i++){
			$uinfo=explode("\t",$groupusers[$i]);
			if($uinfo[1]==$args[1]){
				// check if already op'ed by a more powerfull user
				if($_SESSION[GROUP."_POWER"]<groupPower($uinfo[2])){
					$msg=$MSGARR["SET_BY_MORE_POWER"];
					$_REQUEST['to']=$user;
					break 2;
				}
				
				$grp=$uinfo[0];
				unset($groupusers[$i]);
				break;
			}
		}
		
		if($grp==""){
			$msg="<span class='note'><b>{$args[1]}</b> doesn't belong to any group</span>";
		}else{
			file_put_contents(GROUP_USERS_FILE,implode("\n",$groupusers),LOCK_EX);
			$msg="<span class='note'><b>{$args[1]}</b> has been excluded from the <b>$grp</b> group</span>";
		}
		
		$_REQUEST['to']="$user,{$args[1]}";
		$_REQUEST['updategroup']=array("user"=>$args[1],"group"=>DEFAULT_GROUP);
	break;
	
	case "users":
		// Syntax: \users {group|*}
		$args=explode(" ",preg_replace("/ +/"," ",$strCom),3);
		if(!isset($args[1])){
			$msg=$MSGARR["MISSING_ARGS"];
			$_REQUEST['to']=$user;
			break;
		}
		
		if($args[1]!="*"){
			if(!array_key_exists($args[1],$PERMISSIONS)){
				$msg="<span class='note'>Unkown group name (<b>{$args[1]}</b>)";
				$_REQUEST['to']=$user;
				break;
			}
		}
		
		$groupusers=file(GROUP_USERS_FILE,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		$grp=$args[1];
		$grpHFld="";
		$grpFld="";
		if($args[1]=="*"){
			$grp="All";
			$grpHFld="<th>Group</th>";
			
			// Sort by group name
			function cmp($a, $b){
				$ag=substr($a,0,strpos($a,"\t"));
				$bg=substr($b,0,strpos($b,"\t"));
				return($ag<$bg)?-1:1;
			}
			usort($groupusers, "cmp");
		}
		
		$msg="$grp Users:<br /><table class='users'><tr>$grpHFld<th>User</th><th>Set by</th><th>Date</th></tr>";
		foreach($groupusers as $line){
			$ginfo=explode("\t",$line);
			if($args[1]=="*")$grpFld="<td>{$ginfo[0]}</td>";
			if($ginfo[0]==$args[1] || $args[1]=="*")$msg.="<tr>$grpFld<td>{$ginfo[1]}</td><td>{$ginfo[2]}</td><td>{$ginfo[3]}</td></tr>";
		}
		$msg.="</table>";
		$_REQUEST['to']=$user;
	break;
	
	case "groups":
		// Syntax: \groups
		$msg="Groups / Permissions:<br /><table class='groups'><tr><th>Group</th><th>Power</th><th>Permissions</th></tr>";
		foreach($PERMISSIONS as $k=>$v){
			$power=array_search($k,array_keys($PERMISSIONS));
			$power=($power===false)?0:count($PERMISSIONS)-$power;
			$msg.="<tr><td>$k</td><td>$power</td><td>".implode(", ",$v)."</td></tr>";
		}
		$msg.="</table>";
		$_REQUEST['to']=$user;
	break;
	
	case "rooms":
		// Syntax: \rooms
		$msg="Available rooms:<br /><table class='rooms'><tr><th>Code</th><th>Name</th></tr>";
		global $ROOMS;
		foreach($ROOMS as $k=>$v){
			$msg.="<tr><td>$k</td><td>$v</td></tr>";
		}
		$msg.="</table>";
		$_REQUEST['to']=$user;
	break;
		
	case "cr":
		global $ROOMS;

		// Syntax: \cr {room code|""}
		$args=explode(" ",preg_replace("/ +/"," ",$strCom),2);
		$rc="";
		if(isset($args[1]))$rc=$args[1];

		if(!array_key_exists($rc,$ROOMS)){
			$msg="<span class='note'>Unkown room code (<b>{$rc}</b>)";
			$_REQUEST['to']=$user;
			break;
		}

		postMessage(0,BOT_USERNAME,BOT_DISPLAY_NAME,BOT_GROUP,"","{$_SESSION[USERNAME]} / {$_SESSION[USER_DISPLAY]} left");
			
		$msg="Room changed to <a href='./?rm={$rc}' target='_blank'><b>{$ROOMS[$rc]}</a></b>";
		$_REQUEST['to']=$user;
		$RESP['sc']=array("room",$rc,$ROOMS[$rc]);
	break;
	
	case "help":
		// Syntax: \help
		$msg="Available commands:<br /><table class='help'><tr><th>Command</th><th>Syntax</th></tr>";
		for($i=0;$i<count($PERMISSIONS[$_SESSION[GROUP]]);$i++){
			$synt=isset($COMMANDS[$PERMISSIONS[$_SESSION[GROUP]][$i]])?$COMMANDS[$PERMISSIONS[$_SESSION[GROUP]][$i]]:"";
			$msg.="<tr><td>{$PERMISSIONS[$_SESSION[GROUP]][$i]}</td><td>$synt</td></tr>";
		}
		$msg.="</table>";
		$_REQUEST['to']=$user;
	break;
	
	case "alias":
		// Syntax: \alias {alias name}
		$args=explode(" ",preg_replace("/ +/"," ",$strCom),2);
		if(!isset($args[1])){
			$msg="Your display name is: <b class='note'>{$_SESSION[USER_DISPLAY]}</b>";
			$_REQUEST['to']=$user;
			break;
		}
		
		$alias=file(ALIAS_FILE,FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		for($i=0;$i<count($alias);$i++){
			$ainfo=explode("\t",$alias[$i]);
			if($ainfo[0]==$user){
				unset($alias[$i]);
				break;
			}
		}
		
		// Format: username -> alias name
		if(trim($args[1])=="&quot;&quot;"){
			$_SESSION[USER_DISPLAY]=$user;
			$msg="<b>{$user}</b> has removed his display name";
		}else{
			$_SESSION[USER_DISPLAY]=censoredWords($args[1]);
			$alias[]="{$user}\t{$_SESSION[USER_DISPLAY]}";
			$msg="<b>{$user}</b> has changed his display name to <b>{$_SESSION[USER_DISPLAY]}</b>";
		}		
		
		file_put_contents(ALIAS_FILE,implode("\n",$alias),LOCK_EX);
		$_REQUEST['to']="";
		
		$RESP['sc']=array("displayname", $_SESSION[USER_DISPLAY]);
	break;
}
?>
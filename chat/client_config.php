<?php
/*
ChatBox 6.1.6
Latest update 2013-08-01 06:35
ahmad.murey@gmail.com
www.zaksdg.com
*/
$EMOTIONS=!ALLOW_EMOTIONS?array():array(
	"angel"=>"smiley-angel.png"
	,"confuse"=>"smiley-confuse.png"
	,"cool"=>"smiley-cool.png"
	,"cry"=>"smiley-cry.png"
	,"eek"=>"smiley-eek.png"
	,"evil"=>"smiley-evil.png"
	,"grin"=>"smiley-grin.png"
	,"kiss"=>"smiley-kiss.png"
	,"lol"=>"smiley-lol.png"
	,"mad"=>"smiley-mad.png"
	,"mrgreen"=>"smiley-mr-green.png"
	,"nerd"=>"smiley-nerd.png"
	,"razz"=>"smiley-razz.png"
	,"red"=>"smiley-red.png"
	,"roll"=>"smiley-roll.png"
	,"sad"=>"smiley-sad.png"
	,"sleep"=>"smiley-sleep.png"
	,"sweat"=>"smiley-sweat.png"
	,"wink"=>"smiley-wink.png"
	,"smiley"=>"smiley.png"
	);

// Avatar template params:
// id: user id, u: username, ud: display name, g: group, s: status, z: timezone
$CONFIG=array(
	"BOT_GROUP"=>BOT_GROUP
	,"GUEST_GROUP"=>GUEST_GROUP
	,"ROOM_CODE"=>ROOM_CODE
	,"ROOM_NAME"=>ROOM_NAME
	,"REQUEST_ERROR_RETRIES"=>5 // number of request retries in case of errors (0 to disable)
	,"REQUEST_TIMEOUT"=>15000 // number of milliseconds before the request is timed out (0 to disable)
	,"EMOTIONS_DIR"=>"./emotions" //path to emotions directory
	,"UPDATE_INTERVAL"=>4000 //milliseconds
	,"AFK_UPDATE_INTERVAL"=>8000 //milliseconds
	,"QUE"=>2000 // message queuing time after response (0 to disable)
	,"MAX_EMOTIONS"=>20
	,"SIGNIN_PAGE"=>"./signin.php"// "javascript:openWindow('fb_signin.php','','width=410,height=300')"// 
	,"SIGNIN_MESSAGE"=>"<div class='signinMessage'>Please sign in to start chatting</div>"
	,"KICKOUT_PAGE"=>"./kicked.htm"
	,"AVATAR_TEMPLATE"=>"http://graph.facebook.com/{u}/picture"
	,"NEW_MESSAGE_SOUND"=>"./sounds/new_message.mp3"
	,"PRIVATE_MESSAGE_SOUND"=>"./sounds/private_message.mp3"
	,"USERS_STATUS_CHANGED_SOUND"=>"./sounds/users_status.mp3"
);
?>
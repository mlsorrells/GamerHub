/*
ChatBox 6.1.6
Latest update 2013-08-01 06:35
ahmad.murey@gmail.com
www.zaksdg.com
*/
var CONFIG={};
var EMOTIONS={};
MESSAGES_QUE=[];
MESSAGES_QUE.lastSent=0;
callServer.lastData=null;
callServer.isWorking=false;
callServer.retries=0;

function trim(str){
	return str.replace(/^\s+|\s+$/g,"");
}

function getUserAvatar(user){
	var a=CONFIG.AVATAR_TEMPLATE;
	for(p in user){
		var re=new RegExp("\{"+p+"\}","g");
		a=a.replace(re,user[p]);
	}
	return a;
}

function openWindow(url,name,specs){
	window.open(url,name,specs);
}

// DOM
function dom(id){
	return document.getElementById(id);
}

// getQueryString
function queryString(p){
	var qs=document.location.search.substring(1).split("&");
	for(i=0;i<qs.length;i++){
		var kv=qs[i].split("=");
		if(kv[0]==p){
			return (kv.length>1)?kv[1]:"";
		}
	}
	
	return null;
}

// Insert at carret position
function insertAtCursor(field, value){
	if (document.selection) {
		field.focus();
		sel = document.selection.createRange();
		sel.text = value;
	}else{
		if (field.selectionStart || field.selectionStart == "0") {
			var startPos = field.selectionStart;
			var endPos = field.selectionEnd;
			field.value = field.value.substring(0, startPos) + value + field.value.substring(endPos, field.value.length);
		}else{
			field.value += value;
		}
	}
}

function formatMessage(m){
	// Replace smilies
	m=m.replace(/:(\w+):/g,function(a,b){
		if(b!=null){
			return (b in EMOTIONS)?"<img src='"+CONFIG.EMOTIONS_DIR+"/"+EMOTIONS[b]+"' alt='"+b+"' />":":"+b+":";
		}else{
			return "";
		}
	});
		
	// Replace [url]link[/url]
	m=m.replace(/\[url](.*?)\[\/url]/g,function(a,b){
		if(b==undefined){
			return "";
		}else{
			b=b.replace(/javascript:/i,"");
			if(b.substr(0,7)!="http://" && b.substr(0,8)!="https://")b="http://"+b;
			return "<a class='bbte_url' href='"+b+"' target='_blank'>"+b+"</a>";
		}
	});
	
	// Replace [url=link]description[/url]
	m=m.replace(/\[url\s*=\s*(.*?)\s*](.*?)\[\/url]/g,function(a,b,c){
		if(b!=undefined){
			b=b.replace(/javascript:/i,"");
			if(b.substr(0,7)!="http://" && b.substr(0,8)!="https://")b="http://"+b;
			return "<a href='"+b+"' target='_blank'>"+c+"</a>";
		}else{
			return "";
		}
	});
	
	return m;
}

function loadEmotions(){
	var ec=1;
	dom("pEmotions").innerHTML="";
	for(em in EMOTIONS){
		var img=document.createElement("img");
		img.src=CONFIG.EMOTIONS_DIR+"/"+EMOTIONS[em];
		img.title=em;
		img.code=em;
		img.onclick=function(){
			insertAtCursor(dom("message"),":"+this.code+":");
			dom("message").focus();
		}
		if(ec>CONFIG.MAX_EMOTIONS){
			if(dom("imgExtraEmotions")==null){
				var eimg=document.createElement("img");
				eimg.src="./images/application-icon-large.png";
				eimg.title="Extra Emotions";
				eimg.alt="Extra Emotions";
				eimg.id="imgExtraEmotions";
				eimg.onclick=function(){
					dom("extraEmotions").style.display=(dom("extraEmotions").style.display=="none")?"block":"none";
				}
				dom("pEmotions").appendChild(eimg);
				var eEmotions=document.createElement("div");
				eEmotions.id="extraEmotions";
				eEmotions.style.display="none";
				dom("pEmotions").appendChild(eEmotions);
			}
			
			dom("extraEmotions").appendChild(img);
		}else{
			dom("pEmotions").appendChild(img);
		}
		ec++;
	}
}

function ajax_error(err, isTimeout){
	var err=(isTimeout?"Timeout":"Error")+" <a href='javascript:reconnect()' class='reconnect'>Re-Connect</a>";
	callServer.isWorking=false;
	
	if(CONFIG.REQUEST_ERROR_RETRIES>0){
		if(callServer.retries<CONFIG.REQUEST_ERROR_RETRIES){
			callServer.retries++;
			callServer.lastData.isRetry=true;
			err=(isTimeout?"Timeout":"Re-Connecting")+"<span class='reconnect'>("+callServer.retries+"/"+CONFIG.REQUEST_ERROR_RETRIES+")</span>";
			if(callServer.lastData.o!="check")setTimeout(function(){callServer(callServer.lastData);},1000);
		}else{
			if(check.interval)clearInterval(check.interval);
			callServer.retries=0;
			// clear message queue
			MESSAGES_QUE=[];
			MESSAGES_QUE.lastSent=0;
		}
	}else{
		if(check.interval)clearInterval(check.interval);
	}
	
	dom("serverRes").innerHTML=err+"<span class='status error'>&nbsp;</span>";

}

function ajax_timeout(){
	ajax_error("",true);
}

function reconnect(){
	callServer(callServer.lastData);
	if(check.interval)clearInterval(check.interval);
	check.interval=setInterval(check,CONFIG.UPDATE_INTERVAL);
}

function callServer(data){
	if(data.o=="check" && callServer.isWorking)return false;
	callServer.isWorking=true;
	callServer.lastData=data;
	var q=(MESSAGES_QUE.length>1?"<span class='queue'>("+(MESSAGES_QUE.length-1)+")</span>":"")
	var dic={
			"init":"Initiate",
			"signin":"Sign in",
			"signout":"Sign out",
			"send":"Send"+q,
			"check":"Check"
	};
	var od=dic[data.o];
	if(od==undefined)od=data.o;
	od=(data.isRetry?"Re-":"")+od;
	dom("serverRes").innerHTML=od+"<span class='status "+data.o+"'>&nbsp;</span>";

	var a=new Ajax();
	with(a){
		Method="POST";
		URL="server.php";
		ErrorHandler=ajax_error;
		Timeout=(CONFIG.REQUEST_TIMEOUT||10000);
		OnTimeout=ajax_timeout;
		ResponseFormat="json";
		ResponseHandler=function(res){
			callServer.isWorking=false;
			callServer.retries=0;
			dom("serverRes").innerHTML="<span class='status init'>&nbsp;</span>";

			if(data.o=="send"){
				// since new messages are in the response and the next check may happen sooner after send, we reinitiate the check interval to avoid unnecessary requests
				// also this will reset the interval to the normal value after returning from AFK
				if(check.interval){
					clearInterval(check.interval);
					check.interval=setInterval(check,CONFIG.UPDATE_INTERVAL);
					
				}
				
				dom("currentStatus").className="online";
				dom("currentStatus").title="Online";
				
				if(CONFIG.QUE>0){
					MESSAGES_QUE.lastSent=new Date();
					MESSAGES_QUE.shift();
					if(MESSAGES_QUE.length>0)setTimeout(function(){sendMessage(true);},CONFIG.QUE);
				}
			}
			
			if(typeof(res)=="object" && res!=null){
				processCommand(res.sc);
				processMessages(res.nm);
				processOnlineUsers(res.ol);
			}
		};
		Data=data;
		Send();
	}
}

// Process server commands
function processCommand(sc){
	if(typeof(sc)!="object" || sc.length==0)return false;

	switch(sc[0]){
		case "init":
			EMOTIONS=sc[1];
			CONFIG=sc[2];
			// sc[4] contains user's info
			var isSigned=(sc[4].u!="");
			dom("signinout").value="Sign in";
				dom("signinout").onclick=function(){
				document.location=CONFIG.SIGNIN_PAGE
			};

			loadEmotions();
			if(sc[3] || sc[4].a!=""){
				signin(sc[4]);
			}else{
				dom("chatBox").innerHTML=CONFIG.SIGNIN_MESSAGE;
			}
		break;
		
		case "logged":
			var img=document.createElement("img");
			// sc[1] Contains user's info
			img.src=(sc[1].g==CONFIG.GUEST_GROUP)?"./images/guest.png":getUserAvatar(sc[1]);
			img.title=sc[1].u;
			img.onerror=function(){
				this.src="./images/unreachable.png";
			};
			dom("currentUser").innerHTML="";
			dom("currentUser").appendChild(img);

			var cnt=document.createElement("span");
			cnt.className="nameContainer";
			dom("currentUser").appendChild(cnt);
			
			var nm=document.createElement("span");
			nm.id="displayName";
			nm.innerHTML=sc[1].ud;
			cnt.appendChild(nm);
			
			var sts=document.createElement("span");
			sts.className="online";
			sts.id="currentStatus";
			sts.title="Online";
			cnt.appendChild(sts);
			
			if(sc[1].g==CONFIG.GUEST_GROUP){
				dom("signinout").value="Sign in";
				dom("signinout").onclick=function(){
					document.location=CONFIG.SIGNIN_PAGE;
				};
			}else{
				dom("signinout").value="Sign out";
				dom("signinout").onclick=signout;
			}
			
			dom("chatBox").innerHTML="";
			dom("message").focus();
			
			if(!check.interval)check.interval=setInterval(check,CONFIG.UPDATE_INTERVAL);
		break;
		
		case "signin":
			if(check.interval){
				clearInterval(check.interval);
				delete check.interval;
			}
			document.location=CONFIG.SIGNIN_PAGE;
		break;
		
		case "reload":
			if(check.interval){
				clearInterval(check.interval);
				delete check.interval;
			}
			dom("currentUser").innerHTML="";
			if(!sc[1]){
				dom("chatBox").innerHTML="";
				dom("usersOnline").innerHTML="";
			}
			initiateChatBox(true);
		break;
		
		case "kicked":
			if(check.interval){
				clearInterval(check.interval);
				delete check.interval;
			}
			alert("You are kicked out, Reason: "+sc[1]);
			document.location=CONFIG.KICKOUT_PAGE;
		break;
		
		case "room":
			CONFIG.ROOM_CODE=sc[1];
			CONFIG.ROOM_NAME=sc[2];
			// wake up if command issued from sleep mode
			if(!check.interval)check.interval=setInterval(check,CONFIG.UPDATE_INTERVAL);
		break;
		
		case "clean":
			dom("chatBox").innerHTML="";
		break;
		
		case "away":
			if(check.interval){
				clearInterval(check.interval);
				check.interval=setInterval(check,CONFIG.AFK_UPDATE_INTERVAL);
			}
			
			dom("currentStatus").className="away";
			dom("currentStatus").title="Away from keyboard";
		break;
		
		case "sleep":
			if(check.interval){
				clearInterval(check.interval);
				delete check.interval;
			}
			
			dom("serverRes").innerHTML="Sleep<span class='status sleep'>&nbsp;</span>";
			dom("currentStatus").className="sleep";
			dom("currentStatus").title="Sleep";
		break;
		
		case "wakeup":
			if(!check.interval)check.interval=setInterval(check,CONFIG.UPDATE_INTERVAL);
			dom("currentStatus").className="online";
			dom("currentStatus").title="Online";
		break;
		
		case "error":
			var errmsg=document.createElement("p");
			errmsg.innerHTML=sc[1];
			errmsg.className="errorMessage";
			dom("chatBox").appendChild(errmsg);
			dom("chatBox").scrollTop=dom("chatBox").scrollHeight;
		break;
		
		case "displayname":
			dom("displayName").innerHTML=sc[1];
			// wake up if command issued from sleep mode
			if(!check.interval)check.interval=setInterval(check,CONFIG.UPDATE_INTERVAL);
		break;
	}
}

// Process new messages
function processMessages(nm){
	if(typeof(nm)!="object" || nm.length==0)return false;
	
	var isPrivate=false;
	for(i=0;i<nm.length;i++){
		// Format: sent time -> user id -> username -> display name -> group -> is private -> message
		var seg=nm[i].split("\t");
		// Format the message
		seg[6]=formatMessage(seg[6]);
		
		var msg=document.createElement("p");
		msg.className="chat";
		if(seg[5])isPrivate=true;
		
		var user={	"id":seg[1],
					"u":seg[2],
					"ud":seg[3],
					"g":seg[4]
				};

		seg[6]="<span class='text"+(seg[5]?" private":"")+"'><span class='user grp_"+user.g+"'><b>["+user.g+"]</b> "+user.ud+"</span><br />"+seg[6]+"</span>";
		
		var avatar=(user.g!=CONFIG.BOT_GROUP)?getUserAvatar(user):"./images/bot.png";
		msg.innerHTML="<span class='avatar'><img src='"+avatar+"' title='"+user.u+"' onerror='this.src=\"./images/unreachable.png\"' /></span>"+seg[6]+"<span class='time'>"+seg[0]+"</span>";
		dom("chatBox").appendChild(msg);
	}
	dom("chatBox").scrollTop=dom("chatBox").scrollHeight;
	sp_play(isPrivate?"private_message":"new_message");
}

// Process online users
function processOnlineUsers(ol){
	if(typeof(ol)!="object")return false;
	
	dom("usersOnline").innerHTML="";
	
	var r=document.createElement("p");
	r.className="room";
	r.innerHTML=CONFIG.ROOM_NAME;
	dom("usersOnline").appendChild(r);
	
	if(ol.length>0){
		var cGroup="";
		for(i=0;i<ol.length;i++){
			var uc=ol[i].split("\t");
			var img=document.createElement("img");
			var user={	"id":uc[0],
						"u":uc[1],
						"ud":uc[2],
						"g":uc[3],
						"s":uc[4]
					};
			img.src=(uc[3]==CONFIG.GUEST_GROUP)?"./images/guest.png":getUserAvatar(user);
			img.title="["+user.g+"] "+user.u+" \\ "+user.ud;
			img.className="grp_"+user.g+" " +user.s;
			img.setAttribute("u",user.u);
			img.onclick=function(){
				var tu=dom("touser");
				if ((","+tu.value+",").indexOf(","+this.getAttribute("u")+",")==-1){
					if(tu.value!="" && tu.value.substr(tu.value.length-1,1)!=",")tu.value+=",";
					tu.value+=this.getAttribute("u");
				}
				dom("message").focus();
			}
			
			img.onerror=function(){
				this.src="./images/unreachable.png";
			}
			
			if(user.g!=cGroup){
				var g=document.createElement("p");
				g.className="group grp_"+user.g;
				g.innerHTML=user.g;
				dom("usersOnline").appendChild(g);
				
				cGroup=user.g;
			}
			
			dom("usersOnline").appendChild(img);
		}
	}
	
	var oluCount=document.createElement("p");
	oluCount.className="count";
	var n="No person online";
	if(ol.length==1)n="1 person online";
	if(ol.length>1)n=ol.length+" people online";
	oluCount.innerHTML=n;
	dom("usersOnline").appendChild(oluCount);
	sp_play("users_status");
}

// Initiate
function initiateChatBox(isReloading){
	var data={"o":"init", "rm":queryString("rm")||'', "r":isReloading?1:0};
	callServer(data);
}

// Sign Iin
function signin(user){
	if(user==null)return false;
	var data=user;
	data.o="signin";
	data.rm=CONFIG.ROOM_CODE;
	if(data.id==null)data.id=0;
	if(data.u==null)data.u="";
	if(data.ud==null)data.ud="";
	if(data.z==null)data.z=-(new Date()).getTimezoneOffset()/60;
	if(data.a==null)data.a="";

	callServer(data);
}

// Sign out
function signout(){
	var data={"o":"signout", "rm":CONFIG.ROOM_CODE};
	callServer(data);
}

// Send message
function sendMessage(q){
	if(q==null)q=false;
	var oMsg={};
	
	if(!q){
		var m=trim(dom("message").value);
		var to=trim(dom("touser").value);
		
		if(m=="")return false;
		oMsg={"m":m,"to":to};
	}else{
		oMsg=MESSAGES_QUE[0];
	}
	
	if(CONFIG.QUE==0 || q){
		var data={	"o":"send",
					"rm":CONFIG.ROOM_CODE,
					"m":oMsg.m,
					"to":oMsg.to
				};
		
		callServer(data);
	}
	
	// Queuing
	if(CONFIG.QUE>0 && !q){
		MESSAGES_QUE.push(oMsg);
		if(MESSAGES_QUE.length==1){
			// Check if it's to early to send the new message (lastSent is set after the sending request is completed
			
			if(CONFIG.QUE>0 && (new Date()).valueOf()-MESSAGES_QUE.lastSent.valueOf()<CONFIG.QUE){
				setTimeout(function(){sendMessage(true);},CONFIG.QUE);
			}else{
				sendMessage(true);
			}
		}
	}

	dom("message").value="";
	dom("message").focus();
	return false;
}

function check(){
	var data={"o":"check", "rm":CONFIG.ROOM_CODE};
	callServer(data);
}

// Clear touser field
function clearToUser(){
	dom("touser").value="";
	dom("message").focus();
}

// Sound player related functions
function getPlayer(id) {
	var obj = dom(id);
	if (obj==undefined)return null;
	
	if (obj.sp_load) return obj;
	var mbd=obj.getElementsByTagName("EMBED");
	if (mbd.length>0){
		return mbd[0];
	}else{
		return null;
	}
}

// Initiate sound player
function sp_ready(){
	// check if client's config is initiated (the player may call the function before the configs are loaded), try after a while if not loaded
	if(CONFIG.NEW_MESSAGE_SOUND==null){
		setTimeout(sp_ready,100);
		return false;
	}
	var sp=getPlayer("soundPlayer");
	sp.sp_load(CONFIG.NEW_MESSAGE_SOUND,"new_message");
	sp.sp_load(CONFIG.PRIVATE_MESSAGE_SOUND,"private_message");
	sp.sp_load(CONFIG.USERS_STATUS_CHANGED_SOUND,"users_status");
}

// play sound
function sp_play(name) {
	var sp=getPlayer("soundPlayer");
	if(sp!=null && sp.sp_play!=null)sp.sp_play(name);
}
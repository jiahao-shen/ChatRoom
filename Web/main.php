<!DOCTYPE html>
<?php 
session_start();
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>CugbChat</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/jquery-sinaEmotion-2.1.0.min.css" rel="stylesheet">
    <link href="css/main.css" rel="stylesheet">
</head>
<body>
    <!-- <div class="container">
	    <div class="row clearfix">
	        <div class="col-md-1 column">
	        </div>
	        <div class="col-md-6 column">
	           <div class="thumbnail">
	               <div class="caption" id="dialog"></div>
	           </div>
	           <form onsubmit="onSubmit(); return false;">
	                <select style="margin-bottom:8px" id="client_list">
                        <option value="all">所有人</option>
                    </select>
                    <textarea class="textarea thumbnail" id="textarea"></textarea>
                    <div class="say-btn">
                        <input type="button" class="btn btn-default face pull-left" value="表情" />
                        <input type="submit" class="btn btn-default" value="发表" />
                    </div>
               </form>
               <!-- <div>
               &nbsp;&nbsp;&nbsp;&nbsp;<b>房间列表:</b>（当前在&nbsp;房间<?php echo isset($_GET['room_id']) && intval($_GET['room_id']) > 0 ? intval($_GET['room_id']) : 1; ?>）<br>
               &nbsp;&nbsp;&nbsp;&nbsp;<a href="/?room_id=1">房间1</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/?room_id=2">房间2</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/?room_id=3">房间3</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="/?room_id=4">房间4</a>
               <br><br>
               </div> -->
	        <!-- </div>
	        <div class="col-md-3 column">
	           <div class="thumbnail">
                   <div class="caption" id="userlist"></div>
               </div>
	        </div>
	    </div> -->
    <!-- </div> -->
</body>
    <script type="text/javascript" src="js/swfobject.js"></script>
    <script type="text/javascript" src="js/web_socket.js"></script>
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script tpe="text/javascript" src="js/jquery-sinaEmotion-2.1.0.min.js"></script>
    <script>
        window.onload = function() {
            alert("<?php echo $_SESSION["username"] ?>");
        }
    // WEB_SOCKET_SWF_LOCATION = "swf/WebSocketMain.swf";
    // WEB_SOCKET_DEBUG = true;
    // var ws, username, client_list = {};

    // window.onload = function connect() {
    //    ws = new WebSocket("ws://"+document.domain+":8000");
    //    ws.onopen = onopen;
    //    ws.onmessage = onmessage; 
    //    ws.onclose = function() {
    // 	  console.log("连接关闭，定时重连");
    //       connect();
    //    };
    //    ws.onerror = function() {
    //  	  console.log("出现错误");
    //    };
    // }

    // // 连接建立时发送登录信息
    // function onopen()
    // {
    //     username = "<?php echo $_SESSION["username"]; ?>";
    //     if (username == "") {
    //         window.location.href = "/";
    //     } 
    //     var request = {
    //         "type" : "login",
    //         "username" : username,
    //     }
    //     ws.send(JSON.stringify(request));
    //     console.log("websocket success");
    // }

    // // 服务端发来消息时
    // function onmessage(e)
    // {
    //     console.log(e.data);
    //     var data = JSON.parse(e.data);
    //     switch(data["type"]){
    //         //心跳
    //         case 'ping':
    //             var request = {
    //                 "type" : "pong"
    //             }
    //             ws.send(JSON.stringify(request));
    //             break;
    //         // 登录 更新用户列表
    //         case 'login':
    //             // say(data['client_id'], data['client_name'],  data['client_name']+' 加入了聊天室', data['time']);
    //             // if(data['client_list'])
    //             // {
    //             //     client_list = data['client_list'];
    //             // }
    //             // else
    //             // {
    //             //     client_list[data['client_id']] = data['client_name']; 
    //             // }
    //             // flush_client_list();
    //             // console.log(data['client_name']+"登录成功");
    //             break;
    //         // 发言
    //         case 'say':
    //             //{"type":"say","from_client_id":xxx,"to_client_id":"all/client_id","content":"xxx","time":"xxx"}
    //             // say(data['from_client_id'], data['from_client_name'], data['content'], data['time']);
    //             break;
    //         // 用户退出 更新用户列表
    //         case 'logout':
    //             //{"type":"logout","client_id":xxx,"time":"xxx"}
    //             // say(data['from_client_id'], data['from_client_name'], data['from_client_name']+' 退出了', data['time']);
    //             // delete client_list[data['from_client_id']];
    //             // flush_client_list();
    //     }
    // }

    function send_private(from_user, to_user, content) {
        var request = {
            "type" : "private",
            "from_user" : from_user,
            "to_user" : to_user,
            "content" : content
        }
        ws.send(JSON.stringify(request));
    }

    function send_public(from_user, chat_room, content) {
        var request = {
            "type" : "public"
            "from_user" : from_user,
            "chat_room" : chat_room,
            "content" : content
        }
        ws.send(JSON.stringify(request));
    }

    // 提交对话
    // function onSubmit() {
    //   var input = document.getElementById("textarea");
    //   var to_client_id = $("#client_list option:selected").attr("value");
    //   var to_client_name = $("#client_list option:selected").text();
    //   ws.send('{"type":"say","to_client_id":"'+to_client_id+'","to_client_name":"'+to_client_name+'","content":"'+input.value.replace(/"/g, '\\"').replace(/\n/g,'\\n').replace(/\r/g, '\\r')+'"}');
    //   input.value = "";
    //   input.focus();
    // }

    // 刷新用户列表框
    // function flush_client_list(){
    // 	var userlist_window = $("#userlist");
    // 	var client_list_slelect = $("#client_list");
    // 	userlist_window.empty();
    // 	client_list_slelect.empty();
    // 	userlist_window.append('<h4>在线用户</h4><ul>');
    // 	client_list_slelect.append('<option value="all" id="cli_all">所有人</option>');
    // 	for(var p in client_list){
    //         userlist_window.append('<li id="'+p+'">'+client_list[p]+'</li>');
    //         client_list_slelect.append('<option value="'+p+'">'+client_list[p]+'</option>');
    //     }
    // 	$("#client_list").val(select_client_id);
    // 	userlist_window.append('</ul>');
    // }

    // 发言
    // function say(from_client_id, from_client_name, content, time){
    //     //解析新浪微博图片
    //     content = content.replace(/(http|https):\/\/[\w]+.sinaimg.cn[\S]+(jpg|png|gif)/gi, function(img){
    //         return "<a target='_blank' href='"+img+"'>"+"<img src='"+img+"'>"+"</a>";}
    //     );

    //     //解析url
    //     content = content.replace(/(http|https):\/\/[\S]+/gi, function(url){
    //         if(url.indexOf(".sinaimg.cn/") < 0)
    //             return "<a target='_blank' href='"+url+"'>"+url+"</a>";
    //         else
    //             return url;
    //     }
    //     );

    // 	$("#dialog").append('<div class="speech_item"><img src="http://lorempixel.com/38/38/?'+from_client_id+'" class="user_icon" /> '+from_client_name+' <br> '+time+'<div style="clear:both;"></div><p class="triangle-isosceles top">'+content+'</p> </div>').parseEmotion();
    // }

    // $(function(){
    // 	select_client_id = 'all';
	//     $("#client_list").change(function(){
	//          select_client_id = $("#client_list option:selected").attr("value");
	//     });
    //     $('.face').click(function(event){
    //         $(this).sinaEmotion();
    //         event.stopPropagation();
    //     });
    // });
    </script>
</html>

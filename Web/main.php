<!DOCTYPE html>
<?php 
session_start();
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>CugbChat</title>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
    <script src="https://unpkg.com/element-ui/lib/index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.22.1/moment.min.js"></script>
    <link href="css/chat.css" rel="stylesheet">
</head>
<body>
    <div id="app">
        <el-container>
            <el-aside>
                <el-header>
                    <div style="margin-top: 10px">
                        <img :src="`./image/${username}.png`" @error.once="handleImgLoadFailure($event)" style="width: 40px; float: left; position: relative"/>
                        <h3 style="font-weight: 400; color: white; font-size: 1.6em; margin-left: 50px; color: #d0cdcd; line-height: 50px">
                            {{username}}
                        </h3>
                    </div>
                </el-header>
                <div style="padding: 6px 16px; position: relative;">
                <div class="el-icon-search" style="display: inline-block; font-size: 1.6em; color: white;"></div>
                <input v-model="search" class="search" style="width:70%;"></input>
                </div>
                <transition-group name="flip-list" tag="div">
                <div v-for="chatContact in chatList" :key="chatContact.username">
                    <div 
                        :class="{'chat-item': true, 'chat-item-active': chatContact.username === currentChatItem}"
                        @click="changeChatItem(chatContact)">
                        <img :src="`./image/${chatContact.username}.png`" @error.once="handleImgLoadFailure($event)" style="width: 40px; float: left; position: relative"/>
                        <h3 style="font-weight: 400; color: white;  font-size: 13px;">
                        <span style="vertical-align: top; margin-left: 12px;">
                            {{chatContact.username}}
                        </span>
                        </h3>
                    </div>
                </div>
                </transition-group>
            </el-aside>
            <el-main>
                <div class="chat-area" :style="{height: chatAreaHeight}">
                    <div v-for="(msg, key) in chatLog[currentChatItem]" :key="key" class="content">
                        <div class="time" style="font-size: 10px; color: #b7b4b4;">
                            {{this.window.moment(msg.time).calendar()}}
                        </div>
                        <img :src="`./image/${msg.from}.png`" style="width: 40px; float: right; position: relative"/>
                        <div :class="{bubble: true, 'bubble-my': msg.from === username}">
                            <div style="padding: 9px 13px;">
                                <pre style="margin: 0;white-space: pre-wrap; word-wrap: break-work;">{{msg.content}}</pre>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="chat-box">
                    <el-input 
                        v-model="input" 
                        placeholder="Type something(Press ENTER to send the message)" 
                        type="textarea" 
                        rows="5" 
                        resize="none" 
                        @keypress.native.enter.exact.prevent="sendPrivate()"
                    >
                    </el-input>
                    <el-button type="success" @click="sendPrivate()" class="send">发送</el-button>
                </div>
            </el-main>
        </el-container>
    </div>
</body>
    <script type="text/javascript" src="js/swfobject.js"></script>
    <script type="text/javascript" src="js/web_socket.js"></script>
    <script>
        var bus = new Vue()
        var vm = new Vue({
            el: '#app',
            data: {
                username: '<?php echo $_SESSION["username"]; ?>',
                currentChatItem: '',
                input: '',
                search: '',
                chatLog: {
                   'jiaqi': [ 
                        // {
                        //     content: 'I want to fuck you',
                        //     from: 'shenjiahao',
                        //     to: 'jiaqi',
                        //     time: new Date()
                        // },
                        // {
                        //     content: 'I want to sleep with you',
                        //     from: 'shenjiahao',
                        //     to: 'jiaqi',
                        //     time: new Date()
                        // },
                        // {
                        //     content: 'I want to ML with you',
                        //     from: 'shenjiahao',
                        //     to: 'jiaqi',
                        //     time: new Date()
                        // }
                    ],
                    'baiyu': [ 
                        // {
                        //     content: 'Mao!',
                        //     from: 'baiyu',
                        //     to: 'shenjiahao',
                        //     time: new Date()
                        // },
                        // {
                        //     content: '我是你的大树',
                        //     from: 'baiyu',
                        //     to: 'shenjiahao',
                        //     time: new Date()
                        // }
                    ]
                },
                hiddenChatList: [
                    // {username: 'jiaqi'}, 
                    // {username: 'baiyu'}
                ],
                chatAreaHeight: '700px'
            },
            computed: {
                chatList () {
                    if (this.search) {
                        return this.hiddenChatList.filter(o => o.username.indexOf(this.search) !== -1)
                    } else {
                        return this.hiddenChatList
                    }
                }
            },
            // watch: {
            //     search: function(val) {
            //         if (val === '') {
            //             Vue.set(this, 'chatList', this.hiddenChatList)
            //         }
            //         this.chatList = this.hiddenChatList.filter(o => o.username.indexOf(val) > 0)
            //     },
            //     hiddenChatList: function(val) {
            //         Vue.set(this, 'chatList', val)
            //     }
            // },
            methods: {
                sendPrivate(from = this.username, to = this.currentChatItem, content = this.input) {

                    if (!content || content === '') {
                        this.$message({
                            title: '警告',
                            message: '消息不能为空',
                            type: 'warning'
                        })
                        return;
                    }

                    this.safeAddMessage(this.chatLog, this.currentChatItem, {
                        from,
                        to,
                        content,
                        time: new Date()
                    })
                    this.updateChatList(to)

                    let request = {
                        "type" : "private",
                        "from_user" : from,
                        "to_user" : to,
                        "content" : content
                    }
                    window.ws.send(JSON.stringify(request));
                    this.input = ''
                },
                changeChatItem(ci) {
                    this.currentChatItem = ci.username
                },
                safeAddMessage(src, username, message) {
                    if (!src[username]) {
                        Vue.set(src, username, [message])
                    } else {
                        src[username].push(message)
                    }
                    src[username].sort((a, b) => { 
                        return new Date(a.time) - new Date(b.time)
                    })
                },
                handleImgLoadFailure(e) {
                    e.target.src = '/image/default.png'
                },
                updateChatList(username) {
                    let pos
                    for (let i = 0; i < this.hiddenChatList.length; i++) {
                        if (this.hiddenChatList[i].username === username) {
                            pos = i
                            break
                        }
                    }

                    for (let i = pos; i > 0; i--) {
                        this.hiddenChatList[i] = [this.hiddenChatList[i - 1], this.hiddenChatList[i - 1] = this.hiddenChatList[i]][0]
                    }
                },
                handleResize() {
                    let h = window.document.querySelector('.el-main') && 
                        window.document.querySelector('.el-main').offsetHeight
                    this.chatAreaHeight = h - 220 + 'px'
                }
            },
            updated() {
                this.$nextTick(function () {
                    this.handleResize();
                    let el = document.querySelector('.chat-area')
                    el.scrollTo(0, el.scrollHeight)
                })
            },
            mounted() {
                window.connect()

                window.addEventListener('resize', this.handleResize)

                var self = this
                bus.$on('on-msg', function (data) {
                    self.safeAddMessage(self.chatLog, data.from_user, {
                        from: data.from_user,
                        to: data.to_user,
                        content: data.content,
                        time: new Date(data.time)
                    })

                    self.updateChatList(data.to_user)
                })
                
                bus.$on('on-login', function (data) {
                    self.hiddenChatList.push(...data.users.map(o => ({username: o})))
                    for(let item of data.message_private) {
                        let username;
                        if(item.from_user === self.username) {
                            username = item.to_user
                        } else {
                            username = item.form_user
                        }
                        self.safeAddMessage(self.chatLog, username, {
                            from: item.from_user,
                            to: item.to_user,
                            content: item.content,
                            time: item.time
                        })
                    }
                    self.currentChatItem = self.hiddenChatList[0].username
                })
            }
        })

        // WEB_SOCKET_SWF_LOCATION = "swf/WebSocketMain.swf";
        WEB_SOCKET_DEBUG = true;
        var ws, username, client_list = {};

        function connect() {
            ws = new WebSocket("ws://"+document.domain+":8000");
            ws.onopen = onopen;
            ws.onmessage = onmessage; 
            ws.onclose = function() {
                console.log("连接关闭，定时重连");
                connect();
            };
            ws.onerror = function() {
                console.log("出现错误");
            };
        }

        // 连接建立时发送登录信息
        function onopen()
        {
            username = "<?php echo $_SESSION["username"]; ?>";
            if (username == "") {
                alert("No login user, redirect!")
                window.location.href = "/";
            } 
            var request = {
                "type" : "login",
                "username" : username,
            }
            ws.send(JSON.stringify(request));
            console.log("websocket success");
        }

        // 服务端发来消息时
        function onmessage(e)
        {
            var data = JSON.parse(e.data);
            switch(data["type"]){
                //心跳
                case 'ping':
                    var request = {
                        "type" : "pong"
                    }
                    ws.send(JSON.stringify(request));
                    break;
                case 'login':
                    bus.$emit('on-login', data)
                    break
                // 登录 更新用户列表
                // case 'login':
                    // say(data['client_id'], data['client_name'],  data['client_name']+' 加入了聊天室', data['time']);
                    // if(data['client_list'])
                    // {
                    //     client_list = data['client_list'];
                    // }
                    // else
                    // {
                    //     client_list[data['client_id']] = data['client_name']; 
                    // }
                    // flush_client_list();
                    // console.log(data['client_name']+"登录成功");
                    // break;
                // 发言
                // case 'say':
                    //{"type":"say","from_client_id":xxx,"to_client_id":"all/client_id","content":"xxx","time":"xxx"}
                    // say(data['from_client_id'], data['from_client_name'], data['content'], data['time']);
                    // break;
                // 用户退出 更新用户列表
                // case 'logout':
                    //{"type":"logout","client_id":xxx,"time":"xxx"}
                    // say(data['from_client_id'], data['from_client_name'], data['from_client_name']+' 退出了', data['time']);
                    // delete client_list[data['from_client_id']];
                    // flush_client_list();
            }
        }

        // function send_private(from_user, to_user, content) {
        //     var request = {
        //         "type" : "private",
        //         "from_user" : from_user,
        //         "to_user" : to_user,
        //         "content" : content
        //     }
        //     ws.send(JSON.stringify(request));
        // }

        // function send_public(from_user, chat_room, content) {
        //     var request = {
        //         "type" : "public"
        //         "from_user" : from_user,
        //         "chat_room" : chat_room,
        //         "content" : content
        //     }
        //     ws.send(JSON.stringify(request));
        // }
    </script>
</html>

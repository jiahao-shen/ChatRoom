<!DOCTYPE html>
<?php 
session_start();
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>CugbChat</title>
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
    <link href="css/main.css" rel="stylesheet">
</head>
<body>
    <div id="app">
        <el-container>
            <el-aside>
                <el-header>
                    <div style="margin-top: 10px">
                        <img :src="`image/${username}.png`" @error.once="handleImgLoadFailure($event)" style="width: 40px; float: left; position: relative"/>
                        <h3 style="font-weight: 400; color: white; font-size: 1.6em; margin-left: 50px; color: #d0cdcd; line-height: 50px; max-width: 200px; text-overflow: ellipsis; overflow: hidden;">
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
                        <h3 style="font-weight: 400; color: white;  font-size: 13px; max-width: 180px; text-overflow: ellipsis; overflow: hidden;">
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
                    <div v-for="(msg, key) in chatLog[currentChatItem]" :key="key">
                        <div class="time" style="font-size: 10px; color: #b7b4b4;">
                            {{this.window.moment(msg.time).calendar()}}
                        </div>
                        <div v-if="msg.from === username" style="text-align:right">
                            <img :src="`image/${msg.from}.png`" @error.once="handleImgLoadFailure($event)" style="width: 40px; float: right; position: relative"/>
                            <div class="right-bubble">
                            <div style="padding: 9px 13px;">
                                <pre style="margin: 0;white-space: pre-wrap; word-wrap: break-work;">{{msg.content}}</pre>
                            </div>
                            </div>
                        </div>
                        <div v-else style="text-align:left">
                            <div class="left-bubble">
                            <div style="padding: 9px 13px;">
                                <pre style="margin: 0;white-space: pre-wrap; word-wrap: break-work;">{{msg.content}}</pre>
                            </div>
                            </div>
                            <img :src="`image/${msg.from}.png`" @error.once="handleImgLoadFailure($event)" style="width: 40px; float: left; position: relative"/>
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
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="https://unpkg.com/element-ui/lib/index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.22.1/moment.min.js"></script>
    <script>
        var bus = new Vue()
        var vue = new Vue({
            el: '#app',
            data: {
                username: '<?php echo $_SESSION["username"]; ?>',       //当前用户
                currentChatItem: '',    //当前聊天对象
                input: '',      //输入内容
                search: '',     //搜索内容
                chatLog: {},    //聊天数组
                hiddenChatList: [],         //列表  
                chatAreaHeight: '700px'
            },
            computed: {
                chatList () {       //用户列表
                    if (this.search) {
                        return this.hiddenChatList.filter(o => o.username.indexOf(this.search) !== -1)
                    } else {
                        return this.hiddenChatList
                    }
                }
            },
            methods: {
                sendPrivate(from = this.username, to = this.currentChatItem, content = this.input) {        //发送消息

                    if (!content || content === '') {
                        this.$message({
                            title: '警告',
                            message: '消息不能为空',
                            type: 'warning'
                        })
                        return
                    }

                    this.safeAddMessage(this.chatLog, this.currentChatItem, {       //添加消息
                        from,
                        to,
                        content,
                        time: new Date()
                    })
                    this.updateChatList(to)     //更新列表

                    let request = {
                        "type" : "private",
                        "from_user" : from,
                        "to_user" : to,
                        "content" : content
                    }
                    window.ws.send(JSON.stringify(request))    //发送  
                    this.input = ''
                },
                changeChatItem(ci) {
                    this.currentChatItem = ci.username      //更改聊天对象
                },
                safeAddMessage(src, username, message) {        //发送message
                    if (!src[username]) {
                        Vue.set(src, username, [message])
                    } else {
                        src[username].push(message)
                    }
                    src[username].sort((a, b) => { 
                        return new Date(a.time) - new Date(b.time)
                    })
                },
                handleImgLoadFailure(e) {       //图片错误处理
                    e.target.src = 'image/default.png'
                },
                updateChatList(username) {      //更新用户列表
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
            updated() {     //滑动到底部
                this.$nextTick(function () {
                    this.handleResize()
                    let el = document.querySelector('.chat-area')
                    el.scrollTo(0, el.scrollHeight)
                })
            },
            mounted() {
                window.connect()

                window.addEventListener('resize', this.handleResize)

                var self = this
                bus.$on('on-msg', function (data) {     //接收消息
                    self.safeAddMessage(self.chatLog, data.from_user, {
                        from: data.from_user,
                        to: data.to_user,
                        content: data.content,
                        time: new Date(data.time)
                    })
                    self.updateChatList(data.to_user)
                })
                
                bus.$on('on-login', function (data) {       //登录
                    self.hiddenChatList.push(...data.users.map(o => ({username: o})))
                    for(let item of data.message_private) {
                        self.safeAddMessage(self.chatLog, item.from_user, {
                            from: item.from_user,
                            to: item.to_user,
                            content: item.content,
                            time: item.time
                        })
                        self.safeAddMessage(self.chatLog, item.to_user, {
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

        WEB_SOCKET_DEBUG = true
        var ws, username, client_list = {}

        function connect() {
            ws = new WebSocket("ws://"+document.domain+":8000");
            ws.onopen = onopen
            ws.onmessage = onmessage 
            ws.onclose = function() {
                console.log("连接关闭，定时重连")
                connect()
            }
            ws.onerror = function() {
                console.log("出现错误");
            }
        }

        // 连接建立时发送登录信息
        function onopen() {
            vue.$message({
                message: "登录成功",
                type: "success"
            })
            username = "<?php echo $_SESSION["username"]; ?>"
            if (username == "") {
                alert("No login user, redirect!")
                window.location.href = "/"
            } 
            var request = {
                "type" : "login",
                "username" : username,
            }
            ws.send(JSON.stringify(request))
            console.log("websocket success")
        }

        // 服务端发来消息时
        function onmessage(e) {
            var data = JSON.parse(e.data)
            switch(data["type"]){
                //心跳
                case 'ping':
                    var request = {
                        "type" : "pong"
                    }
                    ws.send(JSON.stringify(request))
                    break
                case 'login':
                    bus.$emit('on-login', data)
                    break
                case 'private':
                    bus.$emit('on-msg', data)
                    break
            }
        }

    </script>
</html>

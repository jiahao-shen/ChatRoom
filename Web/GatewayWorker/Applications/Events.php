<?php

/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

/**
 * 聊天主逻辑
 * 主要是处理 onMessage onClose 
 */
use \GatewayWorker\Lib\Gateway;

class Events
{

    /**
     * 有消息时
     * @param int $client_id
     * @param mixed $message
     */
    public static function onWorkerStart($businessWorker) {
    }

    public static function onMessage($client_id, $message) {
        global $con;
        // 客户端传递的是json数据
        echo $message."\n";
        $request = json_decode($message);
        if (!$request) {
            return;
        }
        switch ($request->type) {
            case "pong":
                return;
            case "login":
                $_SESSION["username"] = $request->username;
                Gateway::bindUid($client_id, $request->username);
                $users = $con->select("users", "username", [
                    "username[!]" => $request->username
                ]);
                $message_private = $con->select("message_private", "*", [
                    "OR" => [
                        "from_user" => $request->username,
                        "to_user" => $request->username
                    ],
                ]);
                $response = array(
                    "type" => "login",
                    "users" => $users,
                    "message_private" => $message_private
                );
                Gateway::sendToCurrentClient(json_encode($response));
                return;
            case "private":
                $con->insert("message_private", [
                    "from_user" => $request->from_user,
                    "to_user" => $request->to_user,
                    "content" => $request->content
                ]);
                $last_id = $con->id();
                echo $last_id."\n";
                $time = $con->get("message_private", "time", [
                    "id" => $last_id
                ]);
                $ret = json_decode($message, true);
                $ret['time'] = $time;
                Gateway::sendToUid($request->to_user, json_encode($ret));
                return;
            case "public":
                $con->insert("message_public", [
                    "from_user" => $request->from_user,
                    "chat_room" => $request->chat_room,
                    "content" => $request->content
                ]);
                Gateway::sendToGroup($request->chat_room, $message);
                return;
        }

    }

    /**
     * 当客户端断开连接时
     * @param integer $client_id 客户端id
     */
    public static function onClose($client_id) {
       // debug
    //    echo "client:{$_SERVER['REMOTE_ADDR']}:{$_SERVER['REMOTE_PORT']} gateway:{$_SERVER['GATEWAY_ADDR']}:{$_SERVER['GATEWAY_PORT']}  client_id:$client_id onClose:''\n";
       
    //    // 从房间的客户端列表中删除
    //    if(isset($_SESSION['room_id']))
    //    {
    //        $room_id = $_SESSION['room_id'];
    //        $new_message = array('type'=>'logout', 'from_client_id'=>$client_id, 'from_client_name'=>$_SESSION['client_name'], 'time'=>date('Y-m-d H:i:s'));
    //        Gateway::sendToGroup($room_id, json_encode($new_message));
    //    }
    }

}

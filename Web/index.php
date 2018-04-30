<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>CugbChat</title>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/css/bootstrap.min.css">
    <link href="https://cdn.bootcss.com/bootstrap-validator/0.5.3/css/bootstrapValidator.css" rel="stylesheet">
    <link rel="stylesheet" href="css/index.css">
</head>

<body>

    <div class="container">
        <h1>CugbChat</h1>

        <form id="login_form">
            <div class="form-group">
                <input type="text" name="username" class="form-control" required="required" placeholder="用户名" v-model="username">
            </div>
            <div class="form-group">
                <input type="password" name="password" class="form-control" required="required" placeholder="密码" v-model="password" @keydown.enter="login">
            </div>
            <button type="button" class="btn btn-lg btn-block btn-primary" @click="login">登录</button>
            <a href="register.php">注册</a>
        </form>

    </div>
</body>

<script src="http://cdn.static.runoob.com/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="http://cdn.static.runoob.com/libs/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdn.bootcss.com/bootstrap-validator/0.5.3/js/bootstrapValidator.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="js/index.js"></script>


</html>
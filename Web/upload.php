<!DOCTYPE>
<html>
<?php session_start(); ?>
<head>
    <meta charset="utf-8">
    <title>CugbChat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://unpkg.com/element-ui/lib/theme-chalk/index.css">
    <link rel="stylesheet" href="css/upload.css"
</head>
<body>
    <div class="container" id="app">
        <img :src="imgUrl">
        
        <el-upload style="height:20px"
            class="upload-demo"
            action="upload_request.php"
            name="img"
            :on-success="uploadSuccess"
            :on-error="uploadFailed"
            >
            <el-button size="large" type="primary">点击上传头像</el-button>
        </el-upload>
        <a id="next" href="main.php">下一步</a>
    </div>
   
</div>
</body>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://unpkg.com/element-ui/lib/index.js"></script>
<script>
    window.onload = function() {
        <?php session_start(); ?>
        let username = "<?php echo $_SESSION["username"]; ?>"
        if (username == null || username == "") {
            window.location.href = "/"
        } 
    }
    var vue = new Vue({
        el: "#app",
        data: {
            imgUrl: "image/default.png",
        },
        methods: {
            uploadSuccess(response, file, fileList) {
                switch(response["type"]) {
                    case "success":
                        this.imgUrl = `image/${response["username"]}.png`
                        break
                    case "failed":
                        this.$message({
                            message: "上传失败",
                            type: "error"
                        })
                        break
                }
            },
            uploadFailed(err, file, fileList) {
                this.$message({
                    message: "上传失败",
                    type: "error"
                })
            } 
        }
    })
</script>
</html>
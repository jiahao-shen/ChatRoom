$(document).ready(function() {
    $("#login_form").bootstrapValidator({
        message: "This value is not valid",
        //excluded:[":hidden",":disabled",":not(visible)"] ,//bootstrapValidator的默认配置
        excluded: ":disabled", //关键配置，表示只对于禁用域不进行验证，其他的表单元素都要验证
        fields: {
            username: {
                message: "用户名不能为空", //默认提示信息
                validators: {
                    notEmpty: {
                        message: "用户名不能为空"
                    },
                    stringLength: {
                        min: 6,
                        max: 20,
                        message: "长度为6到20个字符"
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9_\.]+$/,
                        message: "只能由字母、数字、点和下划线组成"
                    }
                }
            },
            password: {
                message: "密码不能为空",
                validators: {
                    notEmpty: {
                        message: "密码不能为空"
                    },
                    stringLength: {
                        min: 6,
                        max: 20,
                        message: "长度为6-20个字符"
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9_\.]+$/,
                        message: "只能由字母、数字、点和下划线组成"
                    }
                }
            }
        }
    });
});

var vue = new Vue({
    el: "#login_form",
    data: {
        username: "",
        password: ""
    },
    methods: {
        login: function(e) {
            e.preventDefault();
            var bootstrapValidator = $("#login_form").data(
                "bootstrapValidator"
            );
            bootstrapValidator.validate();
            if (bootstrapValidator.isValid()) {
                var request = new URLSearchParams();
                request.append("username", this.username);
                request.append("password", this.password);
                axios
                    .post("login_request.php", request)
                    .then(function(response) {
                        switch(response.data) {
                            case "unknown_error":
                                alert("未知错误请稍后再试");
                                break;
                            case "success":
                                window.location.href = "main.php";
                                break;
                            case "failed":
                                alert("用户名或密码错误");
                                break;
                        }
                    })
                    .then(function(error) {
                        console.log(error);
                    });
            }
        }
    }
});

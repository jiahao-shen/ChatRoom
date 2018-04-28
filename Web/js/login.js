$(document).ready(function () {
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
        login: function (e) {
            e.preventDefault();
            console.log(this.username);
            this.$http.post("php/test.php", {
                "username": this.username,
                "password": this.password
            },{
                emulateJSON: true
            }).then(response => {
                console.log(response);
                alert("you guess");
            }, response => {
                alert("fuck");
            });
        }
    }
});
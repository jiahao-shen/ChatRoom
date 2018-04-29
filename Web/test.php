<?php
header("Content-type:text/html;charset=uft-8");
require_once "mysql.php";

$con->insert("users", [
    "username" => "root",
    "password" => password_hash("258667", PASSWORD_DEFAULT)
]);

$result = $con->get("users", "*", [
    "username" => "root"
]);

if (password_verify("258667", $result["password"])) {
    echo "yes";
} else {
    echo "no";
}
?>
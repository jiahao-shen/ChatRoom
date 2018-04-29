<?php
header("Content-type:text/html;charset=uft-8");
require_once "mysql.php";

if ($con == null) {
    echo "unknown_error";
} else {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $result = $con->insert("users", [
        "username" => $username,
        "password" => password_hash($password, PASSWORD_DEFAULT)
    ]);
    if ($result->rowCount()) {
        echo "success";
    } else {
        echo "failed";
    }
}
?>

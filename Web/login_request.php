<?php
header("Content-type:text/html;charset=uft-8");
require_once "mysql.php";

if ($con == null) {
    echo "unknown_error";
} else {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $result = $con->get("users", "*", [
        "username" => $username,
    ]);
    if ($result != null) {
        if (password_verify($password, $result["password"])) {
            echo "success";
            session_start();
            $_SESSION["username"] = $username;
        } else {
            echo "failed";
        }
    } else {
        echo "failed";
    }
}
?>

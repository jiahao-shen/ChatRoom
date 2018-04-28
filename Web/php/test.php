<?php
header("Content-type:text/html;charset=uft-8");
require_once "mysql.php";

$username = $_POST["username"];
$password = $_POST["password"];
echo $username.",".$password."fuckyou";
?>
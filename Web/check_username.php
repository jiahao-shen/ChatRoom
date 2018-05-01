<?php
header("Content-type:text/html;charset=uft-8");
require_once "mysql.php";

$response = null;
if ($con == null) {
    $response = array(
        "valid" => false
    );
} else {
    $username = $_POST["username"];
    if ($con->get("users", "*", [
        "username" => $username
    ])) {
        $response = array(
            "valid" => false
        );
    } else {
        $response = array(
            "valid" => true
        );
    }
}
echo json_encode($response);
?>

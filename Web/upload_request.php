<?php
session_start();
$username = $_SESSION["username"];
$img = $_FILES["img"];
$response = null;
if (move_uploaded_file($img["tmp_name"], "image/".$username.".png")) {
    $response = array(
        "type" => "success",
        "username" => $username
    );
} else {
    $response = array(
        "type" => "failed"
    );
}
echo json_encode($response);
?>
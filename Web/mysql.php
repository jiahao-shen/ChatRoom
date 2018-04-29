<?php
require_once "Medoo/vendor/autoload.php";

use Medoo\Medoo;

$con = new Medoo([
    "database_type" => "mysql",
    "database_name" => "chat",
    "server" => "127.0.0.1",
    "username" => "root",
    "password" => "258667",
    "charset" => "utf-8"]);
?>

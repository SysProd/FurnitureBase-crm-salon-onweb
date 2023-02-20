<?php
//проверка по прямой проходит пользователь или через include
if (($_SERVER["SCRIPT_NAME"] != 'bd.php' && $_SERVER["SCRIPT_NAME"] != '/php_file/bd.php') && preg_match('/php/i', $_SERVER['SCRIPT_NAME'])) :
    error_reporting(0);
    $db = @mysqli_connect("localhost", "users", "a6vQ2EAaSvNzEmty", "salon") OR die("connection error");
    mysqli_set_charset($db, "utf8");
    error_reporting(E_ALL & ~E_NOTICE);
//eсли прямой переход на bd то перенаправление на index.php
else : header("Location: ./../index.php");endif;
?>
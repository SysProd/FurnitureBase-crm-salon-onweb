<?php
//проверка по прямой проходит пользователь или через include
if ($_SERVER["SCRIPT_NAME"] != '/php_file/check_sessions.php' && preg_match('/php/i', $_SERVER['SCRIPT_NAME'])) :
    session_start();
    require_once "bd.php";

//проверка на существование сесси
    function check_login_pas($log, $pas)
    {
        global $db;
        $login = stripslashes(htmlspecialchars($log));
        $password = stripslashes(htmlspecialchars($pas));
        $sql = "select `id_users` from `users` WHERE `login`='%s' and `password`='%s'";
        $query = sprintf($sql, $login, $password);
        $result = mysqli_query($db, $query) or die(mysqli_error($db));
        $result = mysqli_num_rows($result);
        mysqli_close($db);
        if ($result) return true; else return false;
    }
//eсли прямой переход на check_sessions то перенаправление на index.php
else : header("Location: ./../index.php");endif;
?>
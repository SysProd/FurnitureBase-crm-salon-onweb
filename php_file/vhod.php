<?php require_once "check_sessions.php";
//проверка сессии
if (check_login_pas($_SESSION['login'], $_SESSION['password']) == true) {
    $login = $_SESSION['login'];
    $password = $_SESSION['password'];
} else {
    header("Location: ./../index.php");
}

?>
<html>
<body>
<div class="tex">
    <?php require_once "./menu.php"; ?>
    <div class="inf_block">
        <center>
            Альфа версия CRM ведения Товаров в Магазине
            <br><br><br>
            Данная система еще не запущенна, так как по ней еще ведуться разработки всех блоков и алгоритмов ИС.<br><br>
            Для обеспечения правильной работы производиться тестирование всех блоков системы, <br>если были обнаружены
            какие-то замечания или баги, просьба сообщить <a href="mailto:" if@alterna.su"
            style="color:red;">разработчику системы</a>!!!
        </center>
    </div>
</div>
</body>
</html>
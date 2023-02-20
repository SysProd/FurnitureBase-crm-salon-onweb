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

    <script type="text/javascript">

        var bd_name = 'provider',		// с какой базы брать данные
            after_div = 'table#tovar',	// добавление div элементов после таблицы товаров
            table_name = 'id_provider',	// название таблицы
            kol_page = 1,					// количество страниц на листе
            str_num = 1,					// с какой страницы начать вывод
            sorting = 'sort_asc';			// сортировка по возрастанию

        // шапка таблицы для разных ролей пользователей системы
        if (check_role_in_system() == 'Администратор' || check_role_in_system() == 'Директор') {
            var no_data = 9;				// количество объеденненых колонок в сообщении: "В базе нет данных"
            var top_name_table = '<caption id="add_bd_provider" onmouseover="tooltip.show(\'Для добавления &#34Нового Поставщика&#34, выполните двойное нажатие левой кнопки мыши на эту область\');" onmouseout="tooltip.hide();"> БД Поставщиков </caption><tr id="shapka"><th id="id_provider" class="sort_both">№</th><th id="names" class="sort_both">Поставщик</th><th id="adr_providers" class="sort_both">Адрес</th><th id="number_phone" class="sort_both">№ телефона</th><th id="url_providers" class="sort_both">Сайт</th><th id="email" class="sort_both">E-mail</th><th id="created_by" class="sort_both">Кто Добавил</th><th id="date_add" class="sort_both">Дата Добавления</th><th>Действие</th></tr>';
        } else {
            var no_data = 6;				// количество объеденненых колонок в сообщении: "В базе нет данных"
            var top_name_table = '<caption> БД Поставщиков </caption><tr id="shapka"><th id="id_provider" class="sort_both">№</th><th id="names" class="sort_both">Поставщик</th><th id="adr_providers" class="sort_both">Адрес</th><th id="number_phone" class="sort_both">№ телефона</th><th id="url_providers" class="sort_both">Сайт</th><th id="email" class="sort_both">E-mail</th></tr>';
        }
        // ***конец***
    </script>

    <div class="tovars">
        <div>
            <div style="position: inherit;text-align:left; margin-top: 1%;">
                Показать: <select id="selec_element">
                    <option selected="selected">50</option>
                    <option>100</option>
                    <option>200</option>
                </select>
                элементов
            </div>
            <div style="position: inherit; text-align:right; margin-top: -1.3%; ">
                Поиск: <input id="searc" size=10/>
            </div>
        </div>
        <table cellspacing="0" id="tovar">
        </table>

    </div>

</div>

</body>
</html>
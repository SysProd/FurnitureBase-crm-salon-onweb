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

        var bd_name = 'commodity',		// с какой базы брать данные
            after_div = 'table#tovar', 	// добавление div элементов после таблицы товаров
            table_name = 'id_article',	// название таблицы
            kol_page = 1,					// количество страниц
            str_num = 1,					// с какой страницы начать вывод
            sorting = 'sort_asc';			// сортировка по возрастанию

        // шапка таблицы для разных ролей пользователей системы
        if (check_role_in_system() == 'Администратор' || check_role_in_system() == 'Директор') {
            var no_data = 11;				// количество объеденненых колонок в сообщении: "В базе нет данных"
            var top_name_table = '<caption id="add_bd_commodity" onmouseover="tooltip.show(\'Для добавления &#34Нового Товара&#34, выполните двойное нажатие левой кнопки мыши на эту область\');" onmouseout="tooltip.hide();"> БД Товаров </caption><tr id="shapka"><th id="id_article" class="sort_both">Артикул</th><th id="name_commodity" class="sort_both">Название</th><th id="provisioner" class="sort_both">Поставщик</th><th id="amount" class="sort_both">Кол-во</th><th id="price_purchase" class="sort_both">Закупка</th><th id="price_shipping" class="sort_both">Доставка</th><th id="coefficient" class="sort_both">Коэффициент</th><th id="price_sale" class="sort_both">Реализация</th><th id="created_by" class="sort_both">Кто Добавил</th><th id="date_add" class="sort_both">Дата Добавления</th><th>Действие</th></tr>';
        } else {
            var no_data = 7;				// количество объеденненых колонок в сообщении: "В базе нет данных"
            var top_name_table = '<caption> БД Товаров </caption><tr id="shapka"><th id="id_article" class="sort_both">Артикул</th><th id="name_commodity" class="sort_both">Название</th><th id="amount" class="sort_both">Кол-во</th><th id="provisioner" class="sort_both">Поставщик</th><th id="price_sale" class="sort_both">Рекомендуемая Цена</th><th>Действие</th></tr>';
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
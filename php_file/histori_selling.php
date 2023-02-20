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

        var bd_name = 'check',			// с какой базы брать данные
            after_div = 'table#tovar', 	// добавление div элементов после таблицы товаров
            table_name = 'id_check',		// название таблицы
            kol_page = 1,					// количество страниц на листе
            str_num = 1,					// с какой страницы начать вывод
            sorting = 'sort_desc';		// сортировка по убыванию

        // шапка таблицы для разных ролей пользователей системы
        if (check_role_in_system() == 'Администратор' || check_role_in_system() == 'Директор') {
            var no_data = 11;				// количество объеденненых колонок в сообщении: "В базе нет данных"
            var top_name_table = '<caption onmouseover="tooltip.show(\'Для редактирования &#34Истории продаж&#34, выполните двойное нажатие левой кнопки мыши на эту область\');" onmouseout="tooltip.hide();" > История Продаж </caption><tr id="shapka"><th id="id_check" class="sort_both">№ Чека</th><th id="number_items" class="sort_both">Кол-во позиций (шт)</th><th id="total_commodity" class="sort_both">Кол-во проданных товаров (шт)</th><th id="discount" class="sort_both">Скидка (в руб.)</th><th id="price_total" class="sort_both">Сумма продажи (в руб.)</th><th id="cash" class="sort_both">Вид Расчета</th><th id="comments" class="sort_both">Коментарии</th><th id="date_selling" class="sort_both">Дата продажи</th><th id="user_selling" class="sort_both">Кассир</th><th>Действие</th></tr>';
        } else {
            var no_data = 9;				// количество объеденненых колонок в сообщении: "В базе нет данных"
            var top_name_table = '<caption> История Продаж </caption><tr id="shapka"><th id="id_check" class="sort_both">№ Чека</th><th id="number_items" class="sort_both">Кол-во позиций (шт)</th><th id="total_commodity" class="sort_both">Кол-во проданных товаров (шт)</th><th id="discount" class="sort_both">Скидка (в руб.)</th><th id="price_total" class="sort_both">Сумма продажи (в руб.)</th><th id="cash" class="sort_both">Вид Расчета</th><th id="date_selling" class="sort_both">Дата продажи</th><th id="user_selling" class="sort_both">Кассир</th><th>Действие</th></tr>';
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
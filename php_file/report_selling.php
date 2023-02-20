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
        $(document).ready(function () {


        });
    </script>

    <div class="revenue">
        <div>
            <b>Параметр сортировки:</b>
            <input id="date_from" size=10 placeholder="От:"/>
            <input id="to_date" size=10 placeholder="До:"/>

            <select id="select_providers"
                    onmouseover="tooltip.show('Выбирите поставщика для сортировки в статистике продаж');"
                    onmouseout="tooltip.hide();">
                <option id='0'>По умолчанию</option>
                <?php
                require "./bd.php";
                $result = mysqli_query($db, 'SELECT id_provider,names FROM provider;') or die(false);
                while ($rw = mysqli_fetch_array($result)) {
                    echo "<option id='" . $rw['id_provider'] . "'>" . $rw['names'] . "</option>";
                }
                ?>
            </select>

            <input type="submit" id='revenue_next' value=">>"/>
        </div>
        <div>
            <table cellspacing="0" id="revenue"
                   style="position: relative; text-align:left; margin-top: 5%; margin-left: 13%; width: 10%;">
                <caption> Отчет выручки</caption>

                <tbody>
                <tr id="shapka_revenue">
                    <th id="r_id_check" class="sort_both">№ Чека</th>
                    <th id="r_price_purchase" class="sort_both">Закупка, в руб</th>
                    <th id="r_price_shipping" class="sort_both">Доставка, в руб</th>
                    <th id="r_price_sale" class="sort_both">Реализация, в руб</th>
                    <th id="r_profit" class="sort_both">Прибыль, в руб</th>
                    <th id="r_profit" class="sort_both">Тип Операции</th>
                </tr>
                </tbody>
                <tbody id="top_revenue">
                <tr id="error_revenue">
                    <td colspan="6"
                        style="text-align:center;font-size:15px;font-weight: 600;border: #838383  1px solid; background: -webkit-gradient(linear, left top, left bottom, from(#FAFAFA), to(rgba(118, 244, 0, 0.66)));">
                        Укажите верный параметр сортировки
                    </td>
                </tr>
                </tbody>

            </table>
        </div>
        <div style="position: inherit; top: 54px; left:100%; ">
            <table cellspacing="0" id="sales_statics"
                   style="position: relative; text-align:left; margin-right: 1.3%; width: 150%;">
                <caption> Статистика продаж товара</caption>

                <tbody>
                <tr id="shapka_statics">
                    <th id="s_id_check" class="sort_both">Артикул</th>
                    <th id="s_name_commodity" class="sort_both">Название</th>
                    <th id="s_name_provider" class="sort_both">Поставщик</th>
                    <th id="s_amount" class="sort_both">Кол-во</th>
                    <th id="s_date_add" class="sort_both">Дата продажи</th>
                    <th id="s_created_by" class="sort_both">Кем продано</th>
                </tr>
                </tbody>
                <tbody id="top_statics">
                <tr id="error_statics">
                    <td colspan="6"
                        style="text-align:center;font-size:15px;font-weight: 600;border: #838383  1px solid; background: -webkit-gradient(linear, left top, left bottom, from(#FAFAFA), to(rgba(118, 244, 0, 0.66)));">
                        Укажите верный параметр сортировки
                    </td>
                </tr>
                </tbody>

            </table>
        </div>
    </div>

</div>

</body>
</html>
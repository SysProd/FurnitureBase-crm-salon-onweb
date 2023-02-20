<?php
//проверка пришел ли запрос через ajax
if ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    //header("Content-Type: text/html; charset=utf-8");
    session_start();
    require_once "bd.php";        /*подключение к бд*/
    mb_internal_encoding("UTF-8"); /*кодировка для mb_substr функции*/
//перебор строки для защиты от XSS и SQ-инъекции
    function escape($string)
    {
        global $db;
        if (!get_magic_quotes_gpc())
            return mysqli_real_escape_string($db, $string);
        else
            return mysqli_real_escape_string($db, stripslashes($string));
    }


//функция проверки login и password
    function select_com_date($bar_code)
    {
// Выбрать данные о Товаре с базы
        $com = amount_bd_commodity("SELECT 	`bar_code`,`name_commodity`,`amount`,(`price_purchase`+`price_shipping`) as summa,`coefficient`,`price_sale`,`history_for_com` FROM `commodity` WHERE bar_code = '$bar_code';") or die('error');
        $bd_bar_code = (int)$com[0]['bar_code'];
        $bd_name_commodity = $com[0]['name_commodity'];
        $bd_amount = (int)$com[0]['amount'];
        $bd_summa = (float)$com[0]['summa'];
        $bd_coefficient = (float)$com[0]['coefficient'];
        $bd_price_sale = (float)$com[0]['price_sale'];
        $bd_history_for_com = $com[0]['history_for_com'];
// проверить чтоб колличество символов в истории товара не привышало 65535
        if (strlen($bd_history_for_com) >= 65535) {
            $bd_history_for_com = '||';
        }
        $m = array('bd_bc' => $bd_bar_code, 'bd_nm' => $bd_name_commodity, 'bd_am' => $bd_amount, 'bd_sm' => $bd_summa, 'bd_cf' => $bd_coefficient, 'bd_pr' => $bd_price_sale, 'bd_hst' => $bd_history_for_com);
        return $m;
    }

//функция проверки login и password
    function check_login_pas($log, $pas)
    {
        global $db;
        $sql = "
	select 
			password,
			id_users,
			surnames,
			users.names,
			patronymic,
			functions.names
	from 
			`users` 
	left join `functions` on (`functions`.`id_function`=`users`.`function`)
	WHERE `login`='%s'";
        $query = sprintf($sql, $log);
        $result = mysqli_query($db, $query) or die(mysqli_error($db));
        if (mysqli_num_rows($result)) {
            $e = mysqli_fetch_array($result);
            $output_pass = escape(htmlspecialchars($e[0], ENT_QUOTES));
            if (crypt($pas, $output_pass) == $output_pass) {
                // запоминаем имя пользователя в сессии
                $_SESSION['login'] = escape(htmlspecialchars($log, ENT_QUOTES));
                $_SESSION['password'] = escape(htmlspecialchars($output_pass, ENT_QUOTES));
                $_SESSION['id_users'] = escape(htmlspecialchars($e[1], ENT_QUOTES));
                $_SESSION['inicial'] = escape(htmlspecialchars($e[2], ENT_QUOTES)) . ' ' . mb_substr(escape(htmlspecialchars($e[3], ENT_QUOTES)), 0, 1) . '. ' . mb_substr(escape(htmlspecialchars($e[4], ENT_QUOTES)), 0, 1) . '.';
                $_SESSION['function'] = escape(htmlspecialchars($e[5], ENT_QUOTES));
                return true;
            } else {
                session_destroy();
                return false;
            }
        } else {
            session_destroy();
            return false;
        }
    }

// удаление данных с базы
    function delete_date_bd($sql, $array)
    {
        global $db;
        //проверка какие переменные в массиве
        switch (count($array)) {
            // массив переменных удаление одной позиции в "Корзине" - "array($id_users,$id);"
            case 2:
                $query = sprintf($sql, $array[0], $array[1]);
                break;
            // массив переменных удаление всех позиции в "Корзине" - "array($id_users,$id);"
            case 1:
                $query = sprintf($sql, $array[0]);
                break;
        }
        $result = mysqli_query($db, $query);
        if (mysqli_error($db)) {
            return false;
        }
        if ($result != 'true') {
            return false;
        }
        return true;
    }

//функция подсчета количества данных в базе
    function amount_bd($bd, $str)
    {
        global $db;
        $sql = "select '%s' from `%s`";
        $query = sprintf($sql, $str, $bd);
        $result = mysqli_query($db, $query) or die(mysqli_error($db));
        if ($r = mysqli_num_rows($result)) {
            return $r;
        } else {
            return false;
        }
    }

//функция выборки данных из любой базы
    function selection_bd($sql)
    {
        global $db;
        $query = sprintf($sql);
        $result = mysqli_query($db, $query);

        if (mysqli_error($db)) {
            return false;
        }
        if (mysqli_num_rows($result) == 0) {
            return false;
        }
        $row = array();
        for ($i = 0; $i < mysqli_num_rows($result); $i++) {
            $row[] = mysqli_fetch_array($result, MYSQL_ASSOC);
        }
        return $row;

    }

//функция выборки данных из базы
    function amount_bd_commodity($sqli)
    {
        global $db;
//$result = mysqli_query($db,$sqli) or die(mysqli_error($db));
        $result = mysqli_query($db, $sqli) or die(false);

        if (!$result) {
            exit (false);
            //exit("<p>В базе данных не обнаружено таблицы проверте настройки</p>");
        }

        if (mysqli_num_rows($result) == 0) {
            exit (false);
            //exit('<p>Не обнаружено товаров в базе</p>');
        }

        $row = array();
        for ($i = 0; $i < mysqli_num_rows($result); $i++) {
            $row[] = mysqli_fetch_array($result, MYSQL_ASSOC);
        }
        return $row;
    }

    // функция просчета количества выводимых страниц для контейнера
    function number_pages($count_amount, $count_per_page)
    {
        //$count_amount - сколько всего товаров в БД
        //$count_per_page - с какой страницы начать

        if ($count_amount < $count_per_page) {
            return 'all_one_page';
        }
        //количество страниц
        //(int) - тип данных числовой без остатка
        $number_pages = (int)($count_amount / $count_per_page);
        // % - выводит остаток от деления
        if (($count_amount % $count_per_page) != 0) {
            $number_pages++;
        }
        return $number_pages;
    }

    // функция вывода роли пользователя
    function check_functions_users($log, $pas)
    {
        global $db;
        $sql = "select
				`functions`.`names` 
		from 
				`users`,`functions` 
		WHERE 
				`login`='%s' 
				and `password`='%s' 
				and `users`.`function` = `functions`.`id_function`";

        $query = sprintf($sql, $log, $pas);
        $result = mysqli_query($db, $query);
        if (mysqli_num_rows($result)) {
            if ($e = mysqli_fetch_array($result)) {
                return escape(htmlspecialchars($e[0], ENT_QUOTES));
            } else {
                return 'error_output_functions';
            }
        } else {
            return 'error_bd';
        }

    }


    // функция обновления кол-ва купленных товаров в корзине пользователя
    function update_shoping_card($sql, $array)
    {
        global $db;
        //проверка какие переменные в массиве
        switch (count($array)) {
            // массив переменных "array($new_kol,$id_shop,$id_users)
            case 3:
                $query = sprintf($sql, $array[0], $array[1], $array[2]);
                break;

            case 2:
                $query = sprintf($sql, $array[0], $array[1]);
                break;
            // возврат товара на склад
            case 1:
                $query = sprintf($sql, $array[0]);
                break;
        }

        $result = mysqli_query($db, $query);
        if (mysqli_error($db)) {
            return mysqli_error($db);
        }
        if (!$result) {
            return false;
        } else {
            return true;
        }
    }

    // функция проверки корзины пользователя
    function check_shoping_card($cod, $id)
    {
        global $db;
        $sql_for_shoping = "
			SELECT
								id_shopping					as sh_id_shopping,
								shopping_cart.id_user 		as sh_id_user,
								shopping_cart.bar_code 		as sh_bar_code,
								shopping_cart.amount 		as sh_amount,
								shopping_cart.price_sale 	as sh_price_sale,
								shopping_cart.date_add 		as sh_date_add
			FROM 
								`shopping_cart`
								
			WHERE				`bar_code`='%d' and `id_user`='%d'
			";

        $query = sprintf($sql_for_shoping, $cod, $id);
        $result = mysqli_query($db, $query);
        if (mysqli_error($db)) {
            return false;
        }
        if (mysqli_num_rows($result) == 0) {
            return false;
        }
        return true;
    }

    // функция выборки данных корзины пользователя
    function sample_shoping_card($id)
    {
        global $db;
        $sql_for_shoping = "
			SELECT
								id_shopping					as sh_id_shopping,
								shopping_cart.id_user 		as sh_id_user,
								shopping_cart.bar_code 		as sh_bar_code,
								commodity.id_article 		as sh_id_article,
								commodity.name_commodity	as sh_name,
								shopping_cart.amount 		as sh_amount,
								shopping_cart.price_sale 	as sh_price_sale,
								commodity.amount			as sh_max_amount,
								shopping_cart.date_add 		as sh_date_add
			FROM 
								`shopping_cart`
			left join `commodity` on ( shopping_cart.bar_code = commodity.bar_code)
			WHERE				`id_user`='%d' 
			";

        $query = sprintf($sql_for_shoping, $id);
        $result = mysqli_query($db, $query);
        if (mysqli_error($db)) {
            return false;
        }
        if (mysqli_num_rows($result) == 0) {
            return false;
        }
        $row = array();
        for ($i = 0; $i < mysqli_num_rows($result); $i++) {
            $row[] = mysqli_fetch_array($result, MYSQL_ASSOC);
        }
        return $row;
    }

    // функция сохранения корзины в базу
    function add_shoping_card($cod, $id, $price)
    {
        global $db;
        $sql_add_shoping = "
			INSERT INTO 
							shopping_cart
							(
								id_shopping,
								id_user,
								bar_code,
								amount,
								price_sale,
								date_add
							)
			VALUES 			(
							NULL,
							'%d',
							'%d',
							'%d',
							'%s',
							'%s'
							)
			";

        $query = sprintf($sql_add_shoping, $id, $cod, 1, $price, date('Y-m-d G:i:s'));
        $result = mysqli_query($db, $query);
        if (mysqli_error($db)) {
            return false;
        }
        if (!($result)) {
            return false;
        }
        return true;

        //вернуть id добавленного элемента
        //return mysql_insert_id();
    }

    // функция проведения платежки
    function check_date($type, $bd)
    {
        global $db;
        switch ($bd) {
            case 'users'          :
                $sql = " select id_users from %s WHERE id_users='%s'";
                break;
            case 'counterparties' :
                $sql = " select id_company_inn from %s WHERE id_company_inn='%s'";
                break;
        }

        $query = sprintf($sql, $bd, $type);
        $result = mysqli_query($db, $query);
        if (mysqli_error($db)) {
            return false;
        }
        if (mysqli_num_rows($result) == 0) {
            return false;
        }
        $row = mysqli_fetch_array($result);
        return $row[0];
    }

    // проверка кол-ва "Товара" на складе
    function check_com_shop($bar_code, $kol)
    {
        $rec_commodity = array($_SESSION['id_users']);
        $sql = "select bar_code,amount from `commodity` where bar_code='$bar_code'";
        $r = selection_bd($sql); //or die('error_selection');
        $kol_bd = (int)$r[0]['amount'];
        $sum = $kol_bd - (int)$kol;
// вернуть "true", если кол-во != 0
        if ($kol_bd != 0 && $sum >= 0) return true;
        $sql_delete_shoping = "DELETE FROM `shopping_cart` WHERE id_user = '%d'";
        delete_date_bd($sql_delete_shoping, $rec_commodity); //or die('error_delete');
// вернуть "false", если кол-во == 0
        return false;
    }

    // функция проведения платежки
    function add_selling($arr, $msg)
    {
        global $db;
        switch ($msg) {
            case 'add_check' :
                $sql = "
			INSERT INTO 
							`check`
							(
								id_check,
								id_company_inn,
								number_items,
								total_commodity,
								id_certificates,
								discount,
								price_purchase,
								price,
								price_total,
								cash,
								comments,
								date_selling,
								user_selling,
								created_by,
								history_for_check
							)
			VALUES 			(
							NULL,
							'%s',
							'%d',
							'%d',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s',
							'%d',
							'%s',
							'%s',
							'%d',
							'%d',
							'%s'
							)
			";
                //$add_check = array($bt_contractor,$number_items,$total_commodity,$bt_certificate,$bt_sale,$price_purchase,$price,$price_total,$bt_type_of_tax,$bt_coment,$date_add,$bt_cashier,$id_users,$history_for_check);
                $query = sprintf($sql, $arr[0], $arr[1], $arr[2], $arr[3], $arr[4], $arr[5], $arr[6], $arr[7], $arr[8], $arr[9], $arr[10], $arr[11], $arr[12], $arr[13]);
                break;

            case 'add_selling' :
                $sql = "
			INSERT INTO 
							`selling`
							(
								id_selling,
								id_check,
								number_commodity,
								bar_code,
								amount_celling,
								remainder,
								price_selling
							)
			VALUES 			(
							NULL,
							'%d',
							'%d',
							'%d',
							'%d',
							'%d',
							'%s'
							)
			";
                //array($id_check,$id,$sh_bar_code,$sh_amount,$remainder,$sh_price_sale);
                $query = sprintf($sql, $arr[0], $arr[1], $arr[2], $arr[3], $arr[4], $arr[5]);
                break;
        }

        $result = mysqli_query($db, $query);
        if (mysqli_error($db)) {
            return false;
        }
        if (!($result)) {
            return false;
        }
        //вернуть id добавленного элемента
        return mysqli_insert_id($db);
    }

// блок сохранения в сессию данных
    if (isset($_POST['pas']) && isset($_POST['logi'])) {
        $login = escape(htmlspecialchars($_POST['logi'], ENT_QUOTES));
        $pas = escape(htmlspecialchars($_POST['pas'], ENT_QUOTES));
        echo check_login_pas($login, $pas);
    }
//блок очистки сессии
    if (isset($_POST['del_sessin'])) {
        session_destroy();
        echo "yes";
    }


// Функция возврата товара на склад
    function return_check($id_check, $role, $id_user)
    {

        global $db;
        $inicial = escape(htmlspecialchars($_SESSION['inicial']));
        $history_for_check = "|| " . date('d-m-Y G:i:s') . ">>" . $inicial . ">> Возврат чека.";
        $history_for_com = "|| " . date('d-m-Y G:i:s') . ">>" . $inicial . ">> Возврат товара.";

// проверить кто проводил этот "чек"
        $us_add = amount_bd_commodity("SELECT `user_selling`,`created_by` FROM `check` where `id_check` = '$id_check';") or die('error');
// проверка роли пользователя
// для админа и директора разрешено все чеки возвращать
        if ($role != "Администратор" && $role != "Директор")
// для других только свои чеки
            if ($us_add[0]['user_selling'] != $id_user) exit('error_dip_access');

// начало транзикции
        mysqli_query($db, 'START TRANSACTION;');
// разрешить изменение нескольких строк 
        mysqli_query($db, 'SET SQL_SAFE_UPDATES=0;');
// обновление истории и возврат количества товара
        mysqli_query($db, "UPDATE `check` as CH
LEFT JOIN `selling` AS SL ON CH.id_check = SL.id_check
LEFT JOIN `commodity` AS CM ON CM.bar_code = SL.bar_code
		SET
CH.return_buy = 1,
SL.return_buy = 1,
CH.history_for_check = CONCAT('$history_for_check',CH.history_for_check),
CM.history_for_com = CONCAT('$history_for_com',CM.history_for_com),
CM.amount = CM.amount+SL.amount_celling
		WHERE 
CH.id_check = '$id_check'	and 
CH.return_buy = 0 			and 
SL.return_buy = 0;");
// завершение транзикции
        mysqli_query($db, 'COMMIT;');
// проверка на наличие ошибок
        if (mysqli_error($db)) {
            return 'error';
        }
        return 'ok';
    }

//Выборка с базы данных товаров
    if (isset($_POST['str_amount']) && isset($_POST['sorting']) && isset($_POST['page_start']) && isset($_POST['bd_name']) && isset($_POST['tb_name'])) {

        $tb_name = escape(htmlspecialchars($_POST['tb_name'], ENT_QUOTES));            // название таблицы в бд
        $bd_name = escape(htmlspecialchars($_POST['bd_name'], ENT_QUOTES));            // название базы данных
        $sorting = escape(htmlspecialchars($_POST['sorting'], ENT_QUOTES));            // сортировка по колонке
        $str_start = escape(htmlspecialchars($_POST['page_start'], ENT_QUOTES));        // выбранная страница пользователем
        $str_amount = escape(htmlspecialchars($_POST['str_amount'], ENT_QUOTES));        // количество товаров на одной странице
        $role = check_functions_users(escape(htmlspecialchars($_SESSION['login'], ENT_QUOTES)), escape(htmlspecialchars($_SESSION['password'], ENT_QUOTES)));

// сортировка по возрастанию или убыванию
        switch ($sorting) {
            case 'sort_asc'  :
                $start = ((int)$str_start - 1) * (int)$str_amount;
                $top = 'ORDER BY ' . $tb_name . ' ASC LIMIT  ' . $start . ', ' . $str_amount;
                break;
            case 'sort_desc' :
                $start = ((int)$str_start - 1) * (int)$str_amount;
                $top = 'ORDER BY ' . $tb_name . ' DESC LIMIT ' . $start . ', ' . $str_amount;
                break;
        }


// >>> вывод всех ячеек для "Администратор" и "Директор" <<<<*********************
        if ($role == "Администратор" || $role == "Директор") {

// поиск в базе по введенным данным пользователя		
            switch ($sorting) {
                case 'sort_like_commodity'    :
                    $date_serch = sprintf("%s-%s-%s", mb_substr($str_start, 6, 4), mb_substr($str_start, 3, 2), mb_substr($str_start, 0, 2));
                    $top = 'WHERE (name_commodity LIKE "%' . $str_start . '%" or surnames LIKE "%' . $str_start . '%" or id_article LIKE "%' . $str_start . '%" or price_sale LIKE "%' . $str_start . '%" or provider.names LIKE "%' . $str_start . '%") ORDER BY ' . $tb_name . ' ASC';
                    break;
                case 'sort_like_provider'    :
                    $top = 'WHERE (provider.names LIKE "%' . $str_start . '%" or provider.email LIKE "%' . $str_start . '%" or provider.number_phone LIKE "%' . $str_start . '%" or adr_providers LIKE "%' . $str_start . '%" or url_providers LIKE "%' . $str_start . '%") ORDER BY ' . $tb_name . ' ASC';
                    break;
                case 'sort_like_check'        :
                    $top = 'serch_check';
                    break;
            }
            switch ($bd_name) {
//sql запрос для базы "Товаров"
                case    'commodity'    :

                    $sql = "select
					bar_code,
					id_article,
					name_commodity,
					provider.names as name_provider,
					amount,
					price_purchase,
					price_shipping,
					coefficient,
					price_sale,
					users.names as name_users,
					surnames,
					patronymic,
					commodity.date_add,
					commodity.created_by,
					photo_commodity
					 
			from `commodity` 
			left join `provider` on (`commodity`.`provisioner`=`provider`.`id_provider`)
			left join `users` on (`commodity`.`created_by`=`users`.`id_users`)
			$top
			";
                    $result = amount_bd_commodity($sql) or die('error');                    // результат базы данных
                    foreach ($result as $row) {

// Штрих-Код Товара
                        $bar_code = escape(htmlspecialchars($row['bar_code'], ENT_QUOTES));
// Артикул Товара
                        $id_article = escape(htmlspecialchars($row['id_article'], ENT_QUOTES));
// Название Товара
                        $name_commodity = escape(htmlspecialchars($row['name_commodity'], ENT_QUOTES));
// Название Поствщика
                        $name_provider = escape(htmlspecialchars($row['name_provider'], ENT_QUOTES));
// Количества Товаров
                        $amount = escape(htmlspecialchars($row['amount'], ENT_QUOTES));
// Стоимость Закупки
                        $price_purchase_clear = round(escape(htmlspecialchars($row['price_purchase'], ENT_QUOTES)), 0);
                        $price_purchase = number_format(escape(htmlspecialchars($row['price_purchase'], ENT_QUOTES)), 0, ',', ' ') . " руб.";
// Стоимость Доставки
                        $price_shipping_clear = round(escape(htmlspecialchars($row['price_shipping'], ENT_QUOTES)), 0);
                        $price_shipping = number_format(escape(htmlspecialchars($row['price_shipping'], ENT_QUOTES)), 0, ',', ' ') . " руб.";
// Коэффициент
                        $coefficient = (float)escape(htmlspecialchars($row['coefficient'], ENT_QUOTES));
// Стоимость Реализации
                        $price_sale_clear = round(escape(htmlspecialchars($row['price_sale'], ENT_QUOTES)), 0);
                        $price_sale = number_format(escape(htmlspecialchars($row['price_sale'], ENT_QUOTES)), 0, ',', ' ') . " руб.";
// Кто добавил товар
                        $surnames = escape(htmlspecialchars($row['surnames'], ENT_QUOTES));
// Дата Добавления
                        $date_add = sprintf("%s.%s.%s %s:%s:%s", mb_substr($row['date_add'], 8, 2), mb_substr($row['date_add'], 5, 2), mb_substr($row['date_add'], 0, 4), mb_substr($row['date_add'], 11, 2), mb_substr($row['date_add'], 14, 2), mb_substr($row['date_add'], 17, 2));

//		# Фото товара				>> 	photo_commodity				
//		# Действие					>> 	action

// узнать есть ли товар в корзине
                        $shop = check_shoping_card($bar_code, $_SESSION['id_users']);
                        if (!$shop) {
                            if ($amount == 0) {
                                $active = "<span class='not_available_shoping_cart'></span>";
                            } else {
                                $active = "<a class='add_shop' id='$bar_code' onclick='add_shop($bar_code,$price_sale_clear);' style='cursor: pointer;'><span class='add_shoping_cart'></span></a>";
                            }
                        } else {
                            $active = "<a href='#' class='show_shop' onclick='active_shoping_cart();'><span class='active_shoping_cart'></span></a>";
                        }
                        printf("<tr class='even' onkeydown='if(event.keyCode==9) return false;'>
						<td class='id_article'		id='$bar_code'								>$id_article</td>			
						<td class='name_commodity' 	id='' 										>$name_commodity</td>
						<td class='name_provider' 	id='' 										>$name_provider</td>
						<td class='amount' 			id='' 										>$amount</td>
						<td class='price_purchase' 	id='$price_purchase_clear' 					>$price_purchase</td>
						<td class='price_shipping' 	id='$price_shipping_clear' 					>$price_shipping</td>
						<td class='coefficient' 	id='' 										>$coefficient</td>
						<td class='price_sale' 		id='$price_sale_clear'						>$price_sale</td>
						<td class='created_by' 		id=''										>$surnames</td>
						<td class='date_add' 		id=''										>$date_add</td>
						<td id='' class='action'>
						$active
						<a href='#' class='editing_comm' onclick='editing_commodity($bar_code);'><span class='active_editing_commodity'></span></a>
											</td> </tr>");

                    }
                    break;

//sql запрос для базы "Поставщиков"
                case    'provider'    :


                    $sql = "select
					id_provider,
					provider.names,
					provider.email,
					provider.number_phone,
					adr_providers,
					url_providers,
					surnames,
					date_add
			from `provider`
			left join `users` on (`provider`.`created_by`=`users`.`id_users`)
			$top
			";
                    $result = amount_bd_commodity($sql) or die('error');
                    foreach ($result as $row) {

//		# № Поставщика				>> 	id_provider
                        $id_provider = escape(htmlspecialchars($row['id_provider'], ENT_QUOTES));
//		# Название Поствщика		>> 	names
                        $names = escape(htmlspecialchars($row['names'], ENT_QUOTES));
//		# адресс Поствщика				>> 	adr_providers
                        $adr_providers = escape(htmlspecialchars($row['adr_providers'], ENT_QUOTES));
//		# № телефона Поствщика				>> 	number_phone
                        if (empty($row['number_phone']))
                            $number_phone = '';
                        else
                            $number_phone = sprintf("%s (%s) %s-%s-%s", mb_substr($row['number_phone'], 0, 1), mb_substr($row['number_phone'], 1, 3), mb_substr($row['number_phone'], 4, 3), mb_substr($row['number_phone'], 7, 2), mb_substr($row['number_phone'], 9, 2));

//		# ссылка Поствщика				>> 	url_providers
                        $url_providers = escape(htmlspecialchars($row['url_providers'], ENT_QUOTES));
//		# email Поствщика				>> 	email
                        $email = escape(htmlspecialchars($row['email'], ENT_QUOTES));
//		# Фамилия, кто добавил Поствщика				>> 	surnames
                        $surnames = escape(htmlspecialchars($row['surnames'], ENT_QUOTES));
//		# Дата добавления Поствщика				>> 	date_add
                        $date_add = sprintf("%s.%s.%s %s:%s:%s", mb_substr($row['date_add'], 8, 2), mb_substr($row['date_add'], 5, 2), mb_substr($row['date_add'], 0, 4), mb_substr($row['date_add'], 11, 2), mb_substr($row['date_add'], 14, 2), mb_substr($row['date_add'], 17, 2));
//		# Действие					>> 	action

                        printf("<tr class='even'>
						<td id=''					>$id_provider</td>
						<td id=''					>$names</td>
						<td id=''					>$adr_providers</td>
						<td id=''					>$number_phone</td>
						<td id=''					><a href='$url_providers'  target='_blank' >$url_providers</a></td>
						<td id=''					><a href='mailto:\'$email\' style='color:red;'>$email</a></td>
						<td id='' class='surnames'	>$surnames</td>
						<td id='' class='date_add'	>$date_add</td>
						<td id='' class='action'>
						
						</td>
								</tr>");
                    }
                    break;

//sql запрос для базы "Продажи"
                case    'check'    :
// поиск по чеку
                    if ($top == 'serch_check') {
                        $sql = 'SELECT
                    F.id_selling,
                    F.id_check,
                    F.id_article,
					C.number_items,
					C.total_commodity,
					C.id_certificates,
					C.discount,
					C.price_purchase,
					C.price,
                    C.price_total,
					C.cash,
					C.comments,
					C.date_selling,
                    users.surnames AS user_selling,
					C.return_buy
                    
            FROM `check` as C
            LEFT JOIN `users` ON (C.`user_selling`=`users`.`id_users`),
            ( SELECT id_selling, id_check,id_article 
                FROM `selling` 
            LEFT JOIN `commodity` ON (`commodity`.`bar_code`=`selling`.`bar_code`) ) F
			WHERE 		F.id_check = C.id_check 
					AND (F.id_article LIKE "%' . $str_start . '%" OR C.price_total LIKE "%' . $str_start . '%" OR users.surnames LIKE "%' . $str_start . '%") and C.return_buy = 0
			GROUP BY F.id_check 
			ORDER BY ' . $tb_name . '
			ASC
			';
                    } else {
// обычный вывод чека	
                        $sql = "select
					id_check,
					number_items,
					total_commodity,
					id_certificates,
					discount,
					price_purchase,
					price,
					price_total,
					cash,
					comments,
					date_selling,
					surnames as user_selling,
					return_buy
			from `check`
			left join `users` on (`check`.`user_selling`=`users`.`id_users`)
			where return_buy = 0
			$top
			";

                    }


                    //$result = amount_bd_commodity($sorting,$start,$str_amount,$sql);					// результат базы данных
                    $result = amount_bd_commodity($sql) or die('error');
                    foreach ($result as $row) {
//№ Чека
                        $id_check = escape(htmlspecialchars($row['id_check'], ENT_QUOTES));
//количество позиций
                        $number_items = escape(htmlspecialchars($row['number_items'], ENT_QUOTES));
//количество проданных товаров
                        $total_commodity = escape(htmlspecialchars($row['total_commodity'], ENT_QUOTES));
//№ сертификата
                        $id_certificates = escape(htmlspecialchars($row['id_certificates'], ENT_QUOTES));
//скидка в рублях
                        $discount = number_format(escape(htmlspecialchars($row['discount'], ENT_QUOTES)), 0, '.', ' ') . " руб.";
//сумма продажи
                        $price_total = number_format(escape(htmlspecialchars($row['price_total'], ENT_QUOTES)), 0, '.', ' ') . " руб.";

//вид расчета
                        $cash = escape(htmlspecialchars($row['cash'], ENT_QUOTES));

                        switch ($cash) {
                            case 0 :
                                $cash = 'Наличный';
                                $style_cash = 'style="background: -webkit-gradient(linear, left top, left bottom, from(#F7F9F8), to(#1DF47A));"';
                                break;
                            case 1 :
                                $cash = 'Безналичный';
                                $style_cash = 'style="background: -webkit-gradient(linear, left top, left bottom, from(#F7F9F8), to(#39D4E0));"';
                                break;
                            case 2 :
                                $cash = 'Сертификат';
                                $style_cash = 'style="background: -webkit-gradient(linear, left top, left bottom, from(#F7F9F8), to(#EBF021));"';
                                break;
                            case 3 :
                                $cash = 'Бесплатно';
                                $style_cash = 'style="background: -webkit-gradient(linear, left top, left bottom, from(#F7F9F8), to(#F79EEB));"';
                                break;
                            case 4 :
                                $cash = 'Брак';
                                $style_cash = 'style="background: -webkit-gradient(linear, left top, left bottom, from(#F5FFFB), to(#F96F0D));"';
                                break;
                        }

//Коментарии по продаже
                        $comments = escape(htmlspecialchars($row['comments'], ENT_QUOTES));
//дата продажи
                        $date_selling = sprintf("%s.%s.%s %s:%s:%s", mb_substr($row['date_selling'], 8, 2), mb_substr($row['date_selling'], 5, 2), mb_substr($row['date_selling'], 0, 4), mb_substr($row['date_selling'], 11, 2), mb_substr($row['date_selling'], 14, 2), mb_substr($row['date_selling'], 17, 2));
//Кто продал товар
                        $user_selling = escape(htmlspecialchars($row['user_selling'], ENT_QUOTES));
//Возврат товара
                        $return_buy = escape(htmlspecialchars($row['return_buy'], ENT_QUOTES));
                        printf("<tr class='even'>
						<td id='' class='id_check'>$id_check</td>
						<td id='' class='number_items'>$number_items</td>
						<td id='' class='total_commodity'>$total_commodity</td>
						<td id='' class='discount'>$discount</td>
						<td id='' class='price_total'>$price_total</td>
						<td id='' $style_cash class='cash'>$cash</td>
						<td id='' class='comments'>$comments</td>
						<td id='' class='date_selling'>$date_selling</td>
						<td id='' class='user_selling'>$user_selling</td>
						<td id='' class='action_histori_selling'>
						<a class='show_check' onclick='creat_check_pdf($id_check);'>
						<span class='print_shoping_cart'></span>
						</a>
						<a class='return_check' onclick='return_check($id_check);'>
						<span class='return_shoping_cart'></span>
						</a>
						</td>
								</tr>");

                    }
                    break;
            }

// >>> вывод всех ячеек для других должностей <<<<*********************			
        } else {

// поиск в базе по введенным данным пользователя		
            switch ($sorting) {
                case 'sort_like_commodity':
                    $top = 'WHERE (name_commodity LIKE "%' . $str_start . '%" or id_article LIKE "%' . $str_start . '%" or price_sale LIKE "%' . $str_start . '%" or provider.names LIKE "%' . $str_start . '%") ORDER BY ' . $tb_name . ' ASC';
                    break;
                case 'sort_like_provider':
                    $top = 'WHERE (provider.names LIKE "%' . $str_start . '%" or provider.email LIKE "%' . $str_start . '%" or provider.number_phone LIKE "%' . $str_start . '%" or adr_providers LIKE "%' . $str_start . '%" or url_providers LIKE "%' . $str_start . '%") ORDER BY ' . $tb_name . ' ASC';
                    break;
                case 'sort_like_check':
                    $top = 'WHERE (surnames LIKE "%' . $str_start . '%" or price_total LIKE "%' . $str_start . '%") ORDER BY ' . $tb_name . ' ASC';
                    break;
            }
            switch ($bd_name) {
//sql запрос для базы "Товаров"
                case    'commodity'    :

                    $sql = "select
					bar_code,
					id_article,
					name_commodity,
					provider.names as name_provider,
					amount,
					price_sale,
					photo_commodity
			from `commodity` 
			left join `provider` on (`commodity`.`provisioner`=`provider`.`id_provider`)
			$top
			";

                    //$result = amount_bd_commodity($sorting,$start,$str_amount,$sql);					// результат базы данных
                    $result = amount_bd_commodity($sql) or die('error');
                    foreach ($result as $row) {
// Штрих-Код Товара
                        $bar_code = escape(htmlspecialchars($row['bar_code'], ENT_QUOTES));
// Артикул Товара
                        $id_article = escape(htmlspecialchars($row['id_article'], ENT_QUOTES));
// Название Товара
                        $name_commodity = escape(htmlspecialchars($row['name_commodity'], ENT_QUOTES));
// Название Поствщика
                        $name_provider = escape(htmlspecialchars($row['name_provider'], ENT_QUOTES));
// Количества Товаров
                        $amount = escape(htmlspecialchars($row['amount'], ENT_QUOTES));
// Стоимость Реализации
                        $price_sale = number_format(escape(htmlspecialchars($row['price_sale'], ENT_QUOTES)), 0, '.', ' ') . " руб.";
// Стоимость Реализации
                        $price_sale_clear = escape(htmlspecialchars($row['price_sale'], ENT_QUOTES));

//		# Фото товара				>> 	photo_commodity				
//		# Действие					>> 	action
// узнать есть ли товар в корзине
                        $shop = check_shoping_card($bar_code, $_SESSION['id_users']);
                        if (!$shop) {
                            if ($amount == 0) {
                                $active = "<td class='action'><span class='not_available_shoping_cart'></span></td>";
                            } else {
                                $active = "<td class='action'><a class='add_shop' id='$bar_code' onclick='add_shop($bar_code,$price_sale_clear);' style='cursor: pointer;'><span class='add_shoping_cart'></span></a></td>";
                            }
                        } else {
                            $active = "<td class='action'><a href='#' class='show_shop' onclick='active_shoping_cart();'><span class='active_shoping_cart'></span></a></td>";
                        }
                        printf("<tr class='even'>
						<td id='' class='id_article' id='$bar_code'>$id_article</td>			
						<td id='' class='name_commodity'>$name_commodity</td>
						<td id='$amount' class='amount'>$amount</td>
						<td id='' class='name_provider'>$name_provider</td>
						<td id='$price_sale_clear' class='price_sale'>$price_sale</td>
						$active
								</tr>");
                    }
                    break;

//sql запрос для базы "Поставщиков"
                case    'provider'    :

                    $sql = "select
					id_provider,
					names,
					email,
					number_phone,
					adr_providers,
					url_providers
			from `provider`
			$top
			";

                    //$result = amount_bd_commodity($sorting,$start,$str_amount,$sql);					// результат базы данных
                    $result = amount_bd_commodity($sql) or die('error');
//		# № Поствщика				>> 	id_provider
//		# Название					>> 	names
//		# Email поставщика			>> 	email
//		# Номер телефона поставщика	>> 	number_phone
//		# Адрес Поставщика			>> 	adr_providers
//		# Сайт Поставщика			>> 	url_providers

                    foreach ($result as $row) {

                        if (empty($row['number_phone']))
                            $number_phone = '';
                        else
                            $number_phone = sprintf("%s (%s) %s-%s-%s", mb_substr($row['number_phone'], 0, 1), mb_substr($row['number_phone'], 1, 3), mb_substr($row['number_phone'], 4, 3), mb_substr($row['number_phone'], 7, 2), mb_substr($row['number_phone'], 9, 2));


                        printf("<tr class='even'>
						<td id=''>" . escape(htmlspecialchars($row['id_provider'], ENT_QUOTES)) . "</td>
						<td id=''>" . escape(htmlspecialchars($row['names'], ENT_QUOTES)) . "</td>
						<td id=''>" . escape(htmlspecialchars($row['adr_providers'], ENT_QUOTES)) . "</td>
						<td id=''>" . $number_phone . "</td>
						<td id=''><a href=" . escape(htmlspecialchars($row['url_providers'], ENT_QUOTES)) . "  target='_blank' >" . escape(htmlspecialchars($row['url_providers'], ENT_QUOTES)) . "</a></td>
						<td id=''><a href='mailto:\'" . escape(htmlspecialchars($row['email'], ENT_QUOTES)) . "\' style='color:red;'>" . escape(htmlspecialchars($row['email'], ENT_QUOTES)) . "</a></td>
								</tr>");
                    }
                    break;

//sql запрос для базы "Продажи"
                case    'check'    :

                    $sql = "select
					id_check,
					id_company_inn,
					number_items,
					total_commodity,
					id_certificates,
					discount,
					price_purchase,
					price,
					price_total,
					cash,
					comments,
					date_selling,
					return_buy,
					created_by,
					surnames as user_selling,
					user_selling as US,
					history_for_check
			from `check`			
			left join `users` on (`check`.`user_selling`=`users`.`id_users`)
			where return_buy = 0
			$top
			";

                    //$result = amount_bd_commodity($sorting,$start,$str_amount,$sql);					// результат базы данных
                    $result = amount_bd_commodity($sql) or die('error');
                    foreach ($result as $row) {

//№ Чека
                        $id_check = escape(htmlspecialchars($row['id_check'], ENT_QUOTES));
//количество позиций
                        $number_items = escape(htmlspecialchars($row['number_items'], ENT_QUOTES));
//количество проданных товаров
                        $total_commodity = escape(htmlspecialchars($row['total_commodity'], ENT_QUOTES));
//Скидка в рублях
                        $discount = number_format(escape(htmlspecialchars($row['discount'], ENT_QUOTES)), 0, '.', ' ') . " руб.";
//сумма продажи
                        $price_total = number_format(escape(htmlspecialchars($row['price_total'], ENT_QUOTES)), 0, '.', ' ') . " руб.";
//вид расчета
                        $cash = escape(htmlspecialchars($row['cash'], ENT_QUOTES));
//Кассир
                        $user_selling = escape(htmlspecialchars($row['user_selling'], ENT_QUOTES));
// показать кнопку возврат товара только для тех кто добавил
                        if ($row['US'] == $_SESSION['id_users']) {
                            $mtr = "<a class='return_check' onclick='return_check($id_check);'> <span class='return_shoping_cart'></span> </a>";
                        } else {
                            $mtr = "";
                        }
                        switch ($cash) {
                            case 0 :
                                $cash = 'Наличный';
                                $style_cash = 'style="background: -webkit-gradient(linear, left top, left bottom, from(#F7F9F8), to(#1DF47A));"';
                                break;
                            case 1 :
                                $cash = 'Безналичный';
                                $style_cash = 'style="background: -webkit-gradient(linear, left top, left bottom, from(#F7F9F8), to(#39D4E0));"';
                                break;
                            case 2 :
                                $cash = 'Сертификат';
                                $style_cash = 'style="background: -webkit-gradient(linear, left top, left bottom, from(#F7F9F8), to(#EBF021));"';
                                break;
                            case 3 :
                                $cash = 'Бесплатно';
                                $style_cash = 'style="background: -webkit-gradient(linear, left top, left bottom, from(#F7F9F8), to(#F79EEB));"';
                                break;
                            case 4 :
                                $cash = 'Брак';
                                $style_cash = 'style="background: -webkit-gradient(linear, left top, left bottom, from(#F5FFFB), to(#F96F0D));"';
                                break;
                        }

//дата продажи
                        $date_selling = sprintf("%s.%s.%s %s:%s:%s", mb_substr($row['date_selling'], 8, 2), mb_substr($row['date_selling'], 5, 2), mb_substr($row['date_selling'], 0, 4), mb_substr($row['date_selling'], 11, 2), mb_substr($row['date_selling'], 14, 2), mb_substr($row['date_selling'], 17, 2));

                        printf("<tr class='even'>
						<td id='' class='id_check'>$id_check</td>
						<td id='' class='number_items'>$number_items</td>
						<td id='' class='total_commodity'>$total_commodity</td>
						<td id='' class='discount'>$discount</td>
						<td id='' class='price_total'>$price_total</td>
						<td id='' $style_cash class='cash'>$cash</td>
						<td id='' class='date_selling'>$date_selling</td>
						<td id='' class='user_selling'>$user_selling</td>
						
						<td id='' class='action_histori_selling'>
						<a class='show_check' onclick='creat_check_pdf($id_check);'>
						<span class='print_shoping_cart'></span>
						</a>
							$mtr
						</td>
								</tr>");


                    }
                    break;
            }
        }

    }

//Вывод роли пользователя в системе
    if (isset($_POST['cech']) && isset($_POST['check_role_in_system']) && $_POST['check_role_in_system'] == 'role_system' && isset($_SESSION['id_users'])) {
        $not_sfr = "asaskdfsdgkl4k5l6k3k2J3K24J";
        if (crypt($not_sfr, $_POST['cech']) != $_POST['cech']) exit('error');
        $role = check_functions_users(escape(htmlspecialchars($_SESSION['login'], ENT_QUOTES)), escape(htmlspecialchars($_SESSION['password'], ENT_QUOTES)));
        echo $role;
    }

//Обновление кол-ва купленных товаров в корзине
    if (isset($_POST['cech_update']) && isset($_POST['id_shop']) && isset($_SESSION['id_users']) && isset($_POST['new_kol'])) {
        $not_sfr = "wiepot2324po6jlk5j47";
//проверка подходит ли cach
        if (crypt($not_sfr, $_POST['cech_update']) != $_POST['cech_update']) exit('error');
        $new_kol = escape(htmlspecialchars($_POST['new_kol'], ENT_QUOTES));
        $id_shop = escape(htmlspecialchars($_POST['id_shop'], ENT_QUOTES));
        $id_users = escape(htmlspecialchars($_SESSION['id_users'], ENT_QUOTES));

        $mm = sample_shoping_card($id_users);
// проверка есть ли корзину у этого пользователя
        if (!$mm) exit('error');
        $rec_commodity = array($new_kol, $id_users, $id_shop);
        $update_shop_bd = "
	UPDATE 
			`shopping_cart`

	SET 	`amount`='%d'
	WHERE
			id_user  = '%d' and bar_code = '%d'
			";
        $update_shop = update_shoping_card($update_shop_bd, $rec_commodity);
        if (!$update_shop) exit('error');
        echo 'ok';
    }

//Запись выбраного товара в корзину
    if (isset($_POST['id_commodity']) && isset($_POST['asp']) && isset($_POST['prise_sale']) && isset($_SESSION['id_users'])) {
        $not_sfr = "sdjflksdjf23rsdfkl43";
//проверка подходит ли cach
        if (crypt($not_sfr, $_POST['asp']) != $_POST['asp']) exit('error');
//переменные
        $bar_code = escape(htmlspecialchars($_POST['id_commodity'], ENT_QUOTES));
        $id_user = escape(htmlspecialchars($_SESSION['id_users'], ENT_QUOTES));
        $price_sale = htmlspecialchars($_POST['prise_sale'], ENT_QUOTES);
        $col_shop = check_shoping_card($bar_code, $id_user);
//проверка есть ли в корзине такой же тавар
        if ($col_shop) exit('error');
        $mm = add_shoping_card($bar_code, $id_user, $price_sale);
//запись в корзину
        if (!$mm) exit('error');
        echo 'yes';
    }

// Вывести выбранный товар в корзину
    if (isset($_POST['asps']) && isset($_SESSION['id_users'])) {
        $not_sfr = "asdlkasd32r2k2f23h";
        if (crypt($not_sfr, $_POST['asps']) != $_POST['asps']) exit('error');
        $mm = sample_shoping_card(escape(htmlspecialchars($_SESSION['id_users'], ENT_QUOTES)));
// проверка, есть ли корзина у этого пользователя
        if (!$mm) exit('error');
        $kol = 0;
        $price = 0;
        $sum = 0;
        $su = array();

        foreach ($mm as $val) {
            $kol = (int)$val['sh_amount'];
            $price = (int)$val['sh_price_sale'];
            if (!is_int($kol)) exit('error');
            if (!is_int($price)) exit('error');
            $price = ($val['sh_amount'] * $val['sh_price_sale']);
            $sum = $price + $sum;

            check_com_shop($val['sh_bar_code'], $kol) or die('error_old_query');
            $su['date']['bar_code'][] = $val['sh_bar_code'];        // штрих код
            $su['date']['id_article'][] = $val['sh_id_article'];    // артикул
            $su['date']['name'][] = $val['sh_name'];                // название
            $su['date']['amount'][] = $val['sh_amount'];            // кол-во
            $su['date']['max'][] = $val['sh_max_amount'];            // max кол-во
            $su['date']['price'][] = number_format($val['sh_price_sale'], 0, '.', ' ');    // цена
            $su['date']['in_total'][] = number_format($price, 0, '.', ' ');                // сумма
        }

        $su['result']['summa'][] = number_format($sum, 0, '.', ' ');
        $su['result']['kol'][] = count($mm);

        echo json_encode($su);
    }

// очистка корзины пользователя
    if (isset($_POST['cech']) && isset($_POST['id']) && isset($_SESSION['id_users'])) {
        $not_sfr = "asdlert323erw23555pf";
        if (crypt($not_sfr, $_POST['cech']) == $_POST['cech']) {
            $id = escape(htmlspecialchars($_POST['id'], ENT_QUOTES));
            $id_users = $_SESSION['id_users'];
            switch ($id) {
                case 'del_all_commodity' :
// удаление всех позиций
                    $rec_commodity = array($id_users);
                    $sql_delete_shoping = "
	DELETE 
		FROM 
				`shopping_cart`
		WHERE
				id_user = '%d'";
                    $col_shop = sample_shoping_card($id_users);

                    $delete = delete_date_bd($sql_delete_shoping, $rec_commodity);
                    if ($col_shop && $delete) {
                        echo 'delete';
                    } else {
                        echo 'not_delete';
                    }
                    break;


                default :
// удаление одной позиций
                    $rec_commodity = array($id_users, $id);
                    $sql_delete_shoping = "
	DELETE 
		FROM 
				`shopping_cart`
		WHERE
				id_user = '%d' and bar_code = '%d'";
                    $col_shop = check_shoping_card($id, $id_users);
                    $delete = delete_date_bd($sql_delete_shoping, $rec_commodity);
                    if ($col_shop && $delete) {
                        echo 'delete';
                    } else {
                        echo 'not_delete';
                    }
                    break;
            }

        } else {
            echo 'error';
        }
    }

//Выбор данных с Базы
    if (isset($_POST['sid']) && isset($_POST['name_bd']) && isset($_SESSION['id_users'])) {
        $not_sfr = "asdkre2334kkhn667bv5";
        $su = array();
//проверка подходит ли cach
        if (crypt($not_sfr, $_POST['sid']) != $_POST['sid']) exit('error');

        switch ($_POST['name_bd']) {

            case 'kathir' :

                $sql = "
	SELECT 
			password,
			id_users,
			surnames,
			users.names as nam,
			patronymic,
			functions.names
	FROM 
			`users` 
	left join `functions` on (`functions`.`id_function`=`users`.`function`)
	
	";
//where `functions`.`names`!='Администратор'
                $result = selection_bd($sql);
                if (!$result) exit('error');

                foreach ($result as $val) {

                    $surnames = $val['surnames'];
                    $nam = $val['nam'];
                    $patronymic = $val['patronymic'];
                    $inicial = escape(htmlspecialchars($surnames, ENT_QUOTES)) . ' ' . mb_substr(escape(htmlspecialchars($nam, ENT_QUOTES)), 0, 1) . '.' . mb_substr(escape(htmlspecialchars($patronymic, ENT_QUOTES)), 0, 1) . '.';
                    $id_user = $val['id_users'];
                    $su['date']['names'][$id_user] = $inicial;

                }

                break;

            case 'contractor' :

                $sql = "
	SELECT 
			id_company_inn,
			kpp,
			ogrn,
			okato,
			okpo,
			full_name,
			legal_address,
			actual_address,
			phone_number,
			email_company,
			type_of_tax,
			bik_banks,
			correspondent_account,
			names_banks,
			checking_account
	FROM 	
			`counterparties`
	";

                $result = selection_bd($sql);
                if (!$result) exit('error');

                foreach ($result as $val) {

                    $id_company_inn = $val['id_company_inn'];
                    $full_name = $val['full_name'];
                    $su['date']['names'][$id_company_inn] = escape(htmlspecialchars($full_name, ENT_QUOTES));

                }

                break;

            case 'certificates' :

                $sql = "
		SELECT 
			id_certificates,
			price_certificates,
			implementer,
			date_add,
			created_by
		FROM 
			certificates
	";

                $result = selection_bd($sql);
                if (!$result) exit('error');

                foreach ($result as $val) {

                    $id_certificates = $val['id_certificates'];
                    $price_certificates = $val['price_certificates'];
                    $su['date']['names'][$id_certificates] = number_format(escape(htmlspecialchars($price_certificates, ENT_QUOTES)), 0, '.', ' ');

                }

                break;
            default:
                exit('error');
                break;
        }

        echo json_encode($su);
    }

// Проведение платежки и вывод ссылки на pdf счет
    if (isset($_POST['cech']) && isset($_SESSION['id_users']) && isset($_POST['bt_cashier']) && isset($_POST['bt_contractor']) && isset($_POST['bt_type_of_tax']) && isset($_POST['bt_certificate']) && isset($_POST['bt_sale']) && isset($_POST['bt_coment']) && isset($_POST['date_calendar'])) {
        $not_sfr = "welrk2345kl234h1JKA";
//проверка подходит ли cach
        if (crypt($not_sfr, $_POST['cech']) != $_POST['cech']) exit('error');
        $id_users = $_SESSION['id_users'];
        $inicial = escape(htmlspecialchars($_SESSION['inicial']));
        $col_shop = sample_shoping_card($id_users);
        if (!$col_shop) exit('error');
// роль пользователя в системе
        $role = check_functions_users(escape(htmlspecialchars($_SESSION['login'], ENT_QUOTES)), escape(htmlspecialchars($_SESSION['password'], ENT_QUOTES)));
//вывод всех ячеек для "Администратор" и "Директор"
        if ($role == "Администратор" || $role == "Директор") {
            $date_add = $_POST['date_calendar'];
        } else {
            $date_add = date('Y-m-j G:i:s');
        }

// Кассир
        $bt_cashier = check_date(escape(htmlspecialchars($_POST['bt_cashier'], ENT_QUOTES)), 'users') or die ('error');
// Контрагент
        $bt_contractor = check_date(escape(htmlspecialchars($_POST['bt_contractor'], ENT_QUOTES)), 'counterparties') or die ('error');
// Тип Расчета
        $bt_type_of_tax = escape(htmlspecialchars($_POST['bt_type_of_tax'], ENT_QUOTES));
// Скидка
        $bt_sale = escape(htmlspecialchars($_POST['bt_sale'], ENT_QUOTES));
        $bt_certificate = 0;
        if ($bt_type_of_tax == 3) {
            $bt_type_of_tax = 4;
        }
//проверка если выданно бесплатно
        if ($bt_type_of_tax == 2 && escape(htmlspecialchars($_POST['bt_certificate'], ENT_QUOTES)) == 1) {
            $bt_sale = 0;
            $bt_type_of_tax = 3;
            $bt_certificate = escape(htmlspecialchars($_POST['bt_certificate'], ENT_QUOTES));
        }
//проверка если выданно по сертификату
        if ($bt_type_of_tax == 2 && escape(htmlspecialchars($_POST['bt_certificate'], ENT_QUOTES)) != 1 && escape(htmlspecialchars($_POST['bt_certificate'], ENT_QUOTES)) != 'empty') {
            $bt_certificate = escape(htmlspecialchars($_POST['bt_certificate'], ENT_QUOTES));
        }

        if ($bt_sale == 'empty') {
            $bt_sale = 0;
        }
// Комментарии
        $bt_coment = escape(htmlspecialchars($_POST['bt_coment'], ENT_QUOTES));
        if ($bt_coment == 'empty') {
            $bt_coment = '';
        }
// Кол-во позиций в чеке
        $number_items = count($col_shop);

//* Итоговое кол-во проданых Товаров
        $total_commodity = '';
//* Итоговая сумма продажи
        $price = '';
//* Итоговая закупочная стоимость с доставкой
        $price_purchase = '';

        foreach ($col_shop as $val) {
            check_com_shop($val['sh_bar_code'], (int)$val['sh_amount']) or die('error_old_query');
            $total_commodity = (int)$val['sh_amount'] + $total_commodity;
            $price = $price + ((float)$val['sh_price_sale'] * (int)$val['sh_amount']);
            $bar_code = $val['sh_bar_code'];
            $sql = "select bar_code,amount,price_purchase,price_shipping from `commodity` where bar_code='$bar_code'";
            $r = selection_bd($sql);
            $bd_price_purchase = (float)$r[0]['price_purchase'];
            $bd_price_shipping = (float)$r[0]['price_shipping'];
            $price_purchase = $bd_price_purchase + $bd_price_shipping + $price_purchase;

        }
//* стоимость со скидкой						
        $price_total = (float)$price - (float)$bt_sale;
        $history_for_check = "|| " . date('d-m-Y G:i:s') . ">>" . $inicial . ">> Провел кассовую операцию. ||";

        $add_check = array($bt_contractor, $number_items, $total_commodity, $bt_certificate, $bt_sale, $price_purchase, $price, $price_total, $bt_type_of_tax, $bt_coment, $date_add, $bt_cashier, $id_users, $history_for_check);
        $id_check = add_selling($add_check, 'add_check') or die ('error');
//sql -запрос удаления корзины
        $sql_delete_shoping = "DELETE FROM `shopping_cart` WHERE id_user = '%d'";
        foreach ($col_shop as $id => $val) {
// Штрих-Код Товара
            $sh_bar_code = (int)$val['sh_bar_code'];
// Кол-во Товара
            $sh_amount = (int)$val['sh_amount'];
// Название Товара
            $sh_name = escape(htmlspecialchars($val['sh_name'], ENT_QUOTES));
// MAX кол-во Товаров для продажи
            $sh_max_amount = (int)$val['sh_max_amount'];
// остаток на складе
            $remainder = ($sh_max_amount - $sh_amount);
// Стоимость Товара
            $sh_price_sale = (float)$val['sh_price_sale'];

            $add_selling = array($id_check, ($id + 1), $sh_bar_code, $sh_amount, $remainder, $sh_price_sale);

// запись данных в историю продаж
            $id_selling = add_selling($add_selling, 'add_selling') or die('error');
            $rec_commodity = array($remainder, $sh_bar_code);
            $update_com_bd = "
	UPDATE 
			`commodity`

	SET 	`amount`='%d'
	WHERE
			bar_code  = '%d'
			";
// обновление кол-ва товаров на складе
            update_shoping_card($update_com_bd, $rec_commodity) or die('error');
        }
// очистка корзины
        delete_date_bd($sql_delete_shoping, array($id_users)) or die('error');
// вывод id чека пользователя
        echo $id_check;
    }

// Возврат "Товаров" на склад
    if (isset($_POST['cech']) && isset($_SESSION['id_users']) && isset($_POST['id_check'])) {
        $not_sfr = "asdaewrWEF234";
//проверка подходит ли cach
        if (crypt($not_sfr, $_POST['cech']) != $_POST['cech']) exit('error');

        $id_users = escape(htmlspecialchars($_SESSION['id_users'], ENT_QUOTES));
        $id_check = escape(htmlspecialchars($_POST['id_check'], ENT_QUOTES));
        $role = escape(htmlspecialchars($_SESSION['function'], ENT_QUOTES));

// обработка запроса
//return_check($id_check,$role) or die('error');	
        $m = return_check($id_check, $role, $id_users);
        echo $m;

    }

// Просчет сколько выводить контейнеров для страниц
    if (isset($_POST['str_am']) && isset($_POST['sort']) && isset($_POST['bd_name'])) {
        $str_amount = escape(htmlspecialchars($_POST['str_am'], ENT_QUOTES));        // количество товаров на одной странице
        $sort = escape(htmlspecialchars($_POST['sort'], ENT_QUOTES));                // по какой колонке сортировать бд
        $bd_name = escape(htmlspecialchars($_POST['bd_name'], ENT_QUOTES));        // название базы данных
        $kol_bd = amount_bd($bd_name, $sort) or die('error');                        // вего товаров в базе товаров
        if ($num = number_pages($kol_bd, $str_amount)) echo $num; else echo 'error';
    }


    if (isset($_POST['cech']) && isset($_POST['discount'])) {
        $not_sfr = "asklLKJDSL324sdlfj";
//проверка подходит ли cach
        if (crypt($not_sfr, $_POST['cech']) != $_POST['cech']) exit('error');
        $id_users = $_SESSION['id_users'];
        $discount = (float)$_POST['discount'];
        $sql = "select
					shopping_cart.bar_code,
					((commodity.price_purchase+commodity.price_shipping)*shopping_cart.amount) as cost,
					(commodity.price_sale*shopping_cart.amount) as price_sale
			from `shopping_cart`
			left join `commodity` on (`shopping_cart`.`bar_code`=`commodity`.`bar_code`)
			where id_user='$id_users'
			";
        //$result = amount_bd_commodity('bar_code',0,9999,$sql);
        $result = amount_bd_commodity($sql) or die('error');
        $price = 0;
        $sum = 0;
        foreach ($result as $val) {
            $cost = (float)$val['cost'];
            $price_sale = (float)$val['price_sale'];

            $price = $cost + (float)$price;
            $sum = $price_sale + (float)$sum;
        }

        $itg = ($sum - $discount);
// Проверить, чтоб скидка не привышала себестоимость
        if ($itg >= $price) exit ('ok');
        exit ('not');
    }
// Сохранения в базу измененных данных														
    if (isset($_POST['cech']) && isset($_SESSION['id_users']) && isset($_POST['d_nm']) && isset($_POST['d_am']) && isset($_POST['d_cf']) && isset($_POST['id_elt'])) {
        $not_sfr = "smdfslASd123ASddas";
//проверка подходит ли cach
        if (crypt($not_sfr, $_POST['cech']) != $_POST['cech']) exit('error');

        $bar_code = (int)escape(htmlspecialchars(filter_input(INPUT_POST, 'id_elt', FILTER_SANITIZE_STRING)));
        $d_nm = escape(htmlspecialchars(filter_input(INPUT_POST, 'd_nm', FILTER_SANITIZE_STRING)));
        $d_am = (int)escape(htmlspecialchars(filter_input(INPUT_POST, 'd_am', FILTER_SANITIZE_STRING)));
        $d_cf = (float)escape(htmlspecialchars(filter_input(INPUT_POST, 'd_cf', FILTER_SANITIZE_STRING)));
        $inicial = escape(htmlspecialchars($_SESSION['inicial']));

        $m = select_com_date($bar_code);

        $bd_nm = $m['bd_nm'];
        $bd_am = (int)$m['bd_am'];
        $bd_cf = (float)$m['bd_cf'];

//		# Обновить "Название Товара"
        if ($bd_nm != $d_nm) {

            $m = select_com_date($bar_code);
            $bd_bc = (int)$m['bd_bc'];
            $bd_hst = $m['bd_hst'];

            $new_history = "|| " . date('d-m-Y G:i:s') . ">>" . $inicial . ">> Изменил Название Товара." . $bd_hst;
            $sql = "UPDATE `commodity` as CM	SET CM.`name_commodity` = '$d_nm',CM.`history_for_com` = '$new_history'	WHERE CM.`bar_code` = '$bd_bc';";
            // начало транзикции Обновить "Название Товара"
            mysqli_query($db, 'START TRANSACTION;');
            mysqli_query($db, $sql);
            mysqli_query($db, 'COMMIT;');
            if (mysqli_error($db)) {
                exit ('error');
            }
            echo 'save_name=';
        }

//		# Обновить "Количество Товара"	
        if ($bd_am != $d_am && strlen($d_am) <= 4) {

            $m = select_com_date($bar_code);

            $bd_bc = (int)$m['bd_bc'];
            $bd_hst = $m['bd_hst'];

            $new_history = "|| " . date('d-m-Y G:i:s') . ">>" . $inicial . ">> Изменил Количество Товара." . $bd_hst;
            $sql = "UPDATE `commodity` as CM	SET	CM.`amount` = '$d_am',CM.`history_for_com` = '$new_history'	WHERE CM.`bar_code` = '$bd_bc';";
            // начало транзикции Обновить "Количество Товара"
            mysqli_query($db, 'START TRANSACTION;');
            mysqli_query($db, $sql);
            mysqli_query($db, 'COMMIT;');
            if (mysqli_error($db)) {
                exit ('error');
            }
            echo 'save_amount=';
        }

//		# Обновить "Коэффициент"			
        if ($bd_cf != $d_cf && strlen($d_cf) <= 4) {

            $m = select_com_date($bar_code);

            $bd_bc = (int)$m['bd_bc'];
            $bd_sm = (float)$m['bd_sm'];
            $bd_hst = $m['bd_hst'];

            $new_history = "|| " . date('d-m-Y G:i:s') . ">>" . $inicial . ">> Изменил Коэффициент и Стоимость Реализации Товара." . $bd_hst;
            $new_price_sale = round(($bd_sm * $d_cf), 0);
            $sql = "UPDATE `commodity` as CM	SET CM.`coefficient` = '$d_cf',CM.`price_sale` = '$new_price_sale',CM.`history_for_com` = '$new_history'	WHERE CM.`bar_code` = '$bd_bc';";
            // начало транзикции
            mysqli_query($db, 'START TRANSACTION;');
            mysqli_query($db, $sql);
            mysqli_query($db, 'COMMIT;');
            if (mysqli_error($db)) {
                exit ('error');
            }
            echo 'save_coefficien';
        }
    }
    // Сохранения в базу измененных данных
    if (isset($_POST['cech']) && isset($_SESSION['id_users']) && isset($_POST['d_from']) && isset($_POST['d_date']) && isset($_POST['data_selected'])) {
        $not_sfr = "askdlaskdoiwejfo";
        $su = array();
//проверка подходит ли cach
        if (crypt($not_sfr, $_POST['cech']) != $_POST['cech']) {
            $su['error'][] = 'error_cech';
            echo json_encode($su);
            exit;
        }

        $d_from = escape(htmlspecialchars(filter_input(INPUT_POST, 'd_from', FILTER_SANITIZE_STRING)));
        $d_to = escape(htmlspecialchars(filter_input(INPUT_POST, 'd_date', FILTER_SANITIZE_STRING)));
        $id_users = escape(htmlspecialchars($_SESSION['id_users'], ENT_QUOTES));
        $d_slct = (int)(escape(htmlspecialchars(filter_input(INPUT_POST, 'data_selected', FILTER_SANITIZE_STRING))));
        if ($d_slct == 'default') {
            $d_slct = '';
            $d_slt = '';
        } else {
            $d_slt = "and `commodity`.`provisioner` = '$d_slct'";
            $d_slct = "and provider.id_provider = '$d_slct'";
        }

// Выборка статистики продаж по поставщикам во временном интервале 
        $sql_static = "
SELECT
selling.id_selling			AS s_id,
selling.id_check			AS c_id,
selling.bar_code			AS s_bc,
commodity.id_article		AS c_art,
commodity.name_commodity	AS c_nms,
provider.names				AS p_nms,
date_selling				AS s_ds,
`users`.surnames 			AS s_ins,
(count(selling.bar_code)+(selling.amount_celling))-1 AS s_rt
FROM selling

LEFT JOIN `check` ON (`check`.id_check=`selling`.id_check)
LEFT JOIN `users` ON (`users`.id_users=`check`.user_selling)
LEFT JOIN `commodity` ON (`commodity`.bar_code=`selling`.bar_code)
LEFT JOIN `provider` ON (`provider`.id_provider=`commodity`.provisioner)

where
(date_selling >= 	'$d_from' and
date_selling <=	'$d_to') and
`selling`.return_buy = 0
$d_slct
GROUP BY selling.bar_code HAVING s_rt>=1;
";

// Выборка стоимости реализации во временном интервале по общим параметрам
        $sql_revenue_all = "
SELECT 
	check.id_check				 												AS  c_id,
    ROUND ( sum(commodity.price_purchase*selling.amount_celling) )  			AS c_prh,
    ROUND ( sum(commodity.price_shipping*selling.amount_celling) )  			AS c_spg,
	if(check.cash = 4 OR check.cash = 3,ROUND(sum(commodity.price_purchase*selling.amount_celling)+sum(commodity.price_shipping*selling.amount_celling)),ROUND(check.price_total) ) c_rlz,
	if(check.cash = 4 OR check.cash = 3,-ROUND(sum(commodity.price_purchase*selling.amount_celling)+sum(commodity.price_shipping*selling.amount_celling)),ROUND((sum(selling.price_selling*selling.amount_celling)-check.discount ) - ( sum(commodity.price_purchase*selling.amount_celling) + sum(commodity.price_shipping*selling.amount_celling) ) )   ) c_rvn,
	#ROUND ( check.price_total )			  									AS c_rlz,
	#ROUND ( ( sum(selling.price_selling*selling.amount_celling)-check.discount ) - ( sum(commodity.price_purchase*selling.amount_celling) + sum(commodity.price_shipping*selling.amount_celling) ) ) AS c_rvn,
	check.cash 																	AS c_csh
FROM `check`
LEFT JOIN `selling` ON (`selling`.id_check=`check`.id_check)
LEFT JOIN `commodity` ON (`commodity`.bar_code=`selling`.bar_code)
where (`check`.`date_selling` >= 	'$d_from' and
`check`.`date_selling` <=	'$d_to') and
`check`.`return_buy` = 0
$d_slt
GROUP BY `check`.`id_check`
;
";

// Выборка стоимости реализации во временном интервале по каждому поставщику отдельно
        $sql_revenue_one = "
SELECT 
	check.id_check				  			 									as c_id,
    ROUND ( sum(commodity.price_purchase*selling.amount_celling) ) 			 	as c_prh,
    ROUND ( sum(commodity.price_shipping*selling.amount_celling) ) 			 	as c_spg,
	if(check.cash = 4 OR check.cash = 3, ROUND(sum(commodity.price_purchase*selling.amount_celling)+sum(commodity.price_shipping*selling.amount_celling)),  ROUND( sum(selling.price_selling*selling.amount_celling)-(count(selling.id_check)*(check.discount/check.number_items)) ) )	c_rlz,
	if(check.cash = 4 OR check.cash = 3, -ROUND(sum(commodity.price_purchase*selling.amount_celling)+sum(commodity.price_shipping*selling.amount_celling)),  ROUND( ( sum(selling.price_selling*selling.amount_celling)-(count(selling.id_check)*(check.discount/check.number_items)) ) - ( sum(commodity.price_purchase*selling.amount_celling) + sum(commodity.price_shipping*selling.amount_celling) ) ) )	c_rvn,
	#ROUND ( sum(selling.price_selling*selling.amount_celling)-(count(selling.id_check)*(check.discount/check.number_items)) )	as c_rlz,
    #ROUND ( ( sum(selling.price_selling*selling.amount_celling)-(count(selling.id_check)*(check.discount/check.number_items)) ) - ( sum(commodity.price_purchase*selling.amount_celling) + sum(commodity.price_shipping*selling.amount_celling) ) ) as c_rvn,
    check.cash 																	AS c_csh
FROM `check`

LEFT JOIN `selling` ON (`selling`.id_check=`check`.id_check)
LEFT JOIN `commodity` ON (`commodity`.bar_code=`selling`.bar_code)

where (`check`.`date_selling` >= 	'$d_from' and
`check`.`date_selling` <=	'$d_to') and
`check`.`return_buy` = 0
$d_slt
GROUP BY `check`.`id_check`
;
";
//$static		= selection_bd($sql_static)  or die('error_selection_1');
//$revenue		= selection_bd($sql_revenue) or die('error_selection_2');

        $static = selection_bd($sql_static);
// Обработка запроса 
        if (empty($d_slt)) {
            $revenue = selection_bd($sql_revenue_all);
        } else {
            $revenue = selection_bd($sql_revenue_one);
        }

        if (!$static || !$revenue) {
            $su['error'][] = 'error_selection';
            echo json_encode($su);
            exit;
        }

        foreach ($static as $val) {

            $s_id = $val['s_id'];
            $c_id = $val['c_id'];
            $s_ac = $val['s_ac'];
            $s_bc = $val['s_bc'];
            $с_art = $val['c_art'];
            $c_nms = $val['c_nms'];
            $p_nms = $val['p_nms'];
            $s_rt = $val['s_rt'];
            $s_ds = $val['s_ds'];
            $s_ins = $val['s_ins'];

            $su['static']['id_seling'][] = $s_id;        // id продажи
            $su['static']['bar_code'][] = $s_bc;        // штрих-код
            $su['static']['artical'][] = $с_art;        // артикул
            $su['static']['name_com'][] = $c_nms;        // Название Товара
            $su['static']['name_prv'][] = $p_nms;        // Название Поставщика
            $su['static']['amount_static'][] = $s_rt;        // Кол-во найденых совпадений
            $su['static']['selling_date'][] = $s_ds;        // Дата продажи
            $su['static']['seller'][] = $s_ins;        // Кем Продано

        }
        $prhPrice = '';
        $spgPrice = '';
        $rlzPrice = '';
        $rvnPrice = '';
        foreach ($revenue as $val) {

            $c_id = $val['c_id'];
            $c_prh = $val['c_prh'];
            $c_spg = $val['c_spg'];
            $c_rlz = $val['c_rlz'];
            $c_rvn = $val['c_rvn'];
            $c_csh = $val['c_csh'];

            $su['revenue']['id_check'][] = $c_id;    // № Чека
            $su['revenue']['purchase_price'][] = round($c_prh, 0);    // Закупка, в руб
            $su['revenue']['delivery_price'][] = round($c_spg, 0);    // Доставка, в руб
            $su['revenue']['realz_price'][] = round($c_rlz, 0);    // Реализация, в руб
            $su['revenue']['revenue_price'][] = round($c_rvn, 0);    // Прибыль, в руб
            $su['revenue']['type_cash'][] = (int)$c_csh;        // Тип операции

            $prhPrice += round($c_prh, 0);        // Закупка, в руб
            $spgPrice += round($c_spg, 0);        // Доставка, в руб
            $rlzPrice += round($c_rlz, 0);        // Реализация, в руб
            $rvnPrice += round($c_rvn, 0);        // Прибыль, в руб


        }
        $su['revenue']['id_check'][] = 'summa_price';    // № Чека
        $su['revenue']['purchase_price'][] = $prhPrice;    // Закупка, в руб
        $su['revenue']['delivery_price'][] = $spgPrice;    // Доставка, в руб
        $su['revenue']['realz_price'][] = $rlzPrice;    // Реализация, в руб
        $su['revenue']['revenue_price'][] = $rvnPrice;    // Прибыль, в руб


        echo json_encode($su);
    }


    mysqli_close($db);
} else {
    header("Location: ./../index.php");
}
?>
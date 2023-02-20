<?php session_start();
if (isset($_POST['files_names']) && isset($_POST['cache']) && isset($_SESSION['id_users'])) {
    header('Content-type: text/html; charset=utf-8');
    // ##########################################  Функции #######################################
    //error_reporting(0);
// ################ удаление неподходящего файла #########
    function unlink_all_file($dir)
    {
        if (file_exists($dir)) {
            if (unlink($dir)) {
                return 'del_file';
            } else {
                return 'error_del_file';
            }
        } else {
            return 'no file';
        }
    }

//перебор строки для защиты от XSS и SQ-инъекции
    function escape($string)
    {
        global $db;
        if (!get_magic_quotes_gpc())
            return mysqli_real_escape_string($db, $string);
        else
            return mysqli_real_escape_string($db, stripslashes($string));
    }

// ############# лишних пробелов и символов табуляции ######
    function untab($text)
    {
        $text = str_replace(array("\r\n", "\r", "\n", "\t", '    ', '    '), '', trim($text));
        return str_replace(array('  '), ' ', trim($text));
    }

// ######## обработка excel файла #############
    function output_excel($sstr, $cols, $data, $type_temp)
    {

        $i = 0;
        $arr = array();
        for ($row = 13; $row <= $sstr; ++$row) {

            $i++;
            for ($col = 3; $col <= $cols; ++$col) {
                /*
                # Данные из Excel:
                    * Артикул товара;
                    * Название товара;
                    * Поставшик товара;
                    * Кол-во товара;
                    * Цена закупки за 1 шт в руб. товара;
                    * Стоимость товаров;
                    * Стоимость доставки товара;
                    * Коэффициент;
                    * Рекомендуемая цена;
                    * Комментарии;
                */
                // Первый тип шаблона $type_EUR
                if ($type_temp == '1') {
                    switch ($col) {
                        case 3 :
                            if ($data->val($row, $col, 0) == '') $arr[$i]['artikul'] = ''; else    $arr[$i]['artikul'] = untab(stripslashes(htmlspecialchars($data->val($row, $col, 0))));
                            break;
                        case 4 :
                            if ($data->val($row, $col, 0) == '') $arr[$i]['name'] = ''; else    $arr[$i]['name'] = untab(stripslashes(htmlspecialchars($data->val($row, $col, 0))));
                            break;
                        case 5 :
                            if ($data->val($row, $col, 0) == '') $arr[$i]['provider'] = ''; else    $arr[$i]['provider'] = untab(stripslashes(htmlspecialchars($data->val($row, $col, 0))));
                            break;
                        case 6 :
                            if ($data->val($row, $col, 0) == '' || $data->val($row, $col, 0) == 0) $arr[$i]['kol_vo'] = ''; else    $arr[$i]['kol_vo'] = stripslashes(htmlspecialchars($data->val($row, $col, 0)));
                            break;
                        case 8 :
                            if ($data->raw($row, $col, 0) == '') $arr[$i]['price_purchase'] = ''; else    $arr[$i]['price_purchase'] = stripslashes(htmlspecialchars(number_format($data->raw($row, $col, 0), 0, '.', '')));
                            break;
                        case 9 :
                            if ($data->raw($row, $col, 0) == '') $arr[$i]['price'] = ''; else    $arr[$i]['price'] = stripslashes(htmlspecialchars(number_format($data->raw($row, $col, 0), 0, '.', '')));
                            break;
                        case 10 :
                            if ($data->raw($row, $col, 0) == '') $arr[$i]['price_deliver'] = ''; else    $arr[$i]['price_deliver'] = stripslashes(htmlspecialchars(number_format($data->raw($row, $col, 0), 0, '.', '')));
                            break;
                        case 11 :
                            if ($data->val($row, $col, 0) == '' || $data->val($row, $col, 0) == 0) $arr[$i]['coefficient'] = ''; else    $arr[$i]['coefficient'] = stripslashes(htmlspecialchars($data->val($row, $col, 0)));
                            break;
                        case 12 :
                            if ($data->raw($row, $col, 0) == '') $arr[$i]['price_sale'] = ''; else    $arr[$i]['price_sale'] = stripslashes(htmlspecialchars(number_format($data->raw($row, $col, 0), 0, '.', '')));
                            break;
                        case 13 :
                            if ($data->val($row, $col, 0) == '') $arr[$i]['coment'] = ''; else    $arr[$i]['coment'] = untab(stripslashes(htmlspecialchars($data->val($row, $col, 0))));
                            break;
                    }
                }

                // Второй тип шаблона $type_RUB
                if ($type_temp == '2') {
                    switch ($col) {
                        case 3 :
                            if ($data->val($row, $col, 0) == '') $arr[$i]['artikul'] = ''; else    $arr[$i]['artikul'] = untab(stripslashes(htmlspecialchars($data->val($row, $col, 0))));
                            break;
                        case 4 :
                            if ($data->val($row, $col, 0) == '') $arr[$i]['name'] = ''; else    $arr[$i]['name'] = untab(stripslashes(htmlspecialchars($data->val($row, $col, 0))));
                            break;
                        case 5 :
                            if ($data->val($row, $col, 0) == '') $arr[$i]['provider'] = ''; else    $arr[$i]['provider'] = untab(stripslashes(htmlspecialchars($data->val($row, $col, 0))));
                            break;
                        case 6 :
                            if ($data->val($row, $col, 0) == '' || $data->val($row, $col, 0) == 0) $arr[$i]['kol_vo'] = ''; else    $arr[$i]['kol_vo'] = stripslashes(htmlspecialchars($data->val($row, $col, 0)));
                            break;
                        case 7 :
                            if ($data->raw($row, $col, 0) == '') $arr[$i]['price_purchase'] = ''; else    $arr[$i]['price_purchase'] = stripslashes(htmlspecialchars(number_format($data->raw($row, $col, 0), 0, '.', '')));
                            break;
                        case 8 :
                            if ($data->raw($row, $col, 0) == '') $arr[$i]['price'] = ''; else    $arr[$i]['price'] = stripslashes(htmlspecialchars(number_format($data->raw($row, $col, 0), 0, '.', '')));
                            break;
                        case 9 :
                            if ($data->raw($row, $col, 0) == '') $arr[$i]['price_deliver'] = ''; else    $arr[$i]['price_deliver'] = stripslashes(htmlspecialchars(number_format($data->raw($row, $col, 0), 0, '.', '')));
                            break;
                        case 10 :
                            if ($data->val($row, $col, 0) == '' || $data->val($row, $col, 0) == 0) $arr[$i]['coefficient'] = ''; else    $arr[$i]['coefficient'] = stripslashes(htmlspecialchars($data->val($row, $col, 0)));
                            break;
                        case 11 :
                            if ($data->raw($row, $col, 0) == '') $arr[$i]['price_sale'] = ''; else    $arr[$i]['price_sale'] = stripslashes(htmlspecialchars(number_format($data->raw($row, $col, 0), 0, '.', '')));
                            break;
                        case 12 :
                            if ($data->val($row, $col, 0) == '') $arr[$i]['coment'] = ''; else    $arr[$i]['coment'] = untab(stripslashes(htmlspecialchars($data->val($row, $col, 0))));
                            break;
                    }
                }

            }
        }
        return $arr;
    }

// ############# Поиск ошибок в загруженном Excel файле ######		
    function error_result_excel($arr)
    {

        $error = array();
        $repeat = array();

        foreach ($arr as $id => $val) {
            // Запись в массив найденные артикул для пойска повтарений
            $repeat[$id] = $val['artikul'];
            //$coment 		= $val['coment'];
            if (empty($val['artikul'])) $error['error_empty'][] = array('id' => $id, 'error' => 'Артикула');
            if (empty($val['name'])) $error['error_empty'][] = array('id' => $id, 'error' => 'Названия');
            if (empty($val['provider'])) $error['error_empty'][] = array('id' => $id, 'error' => 'Названия Поставщика');
            if (empty($val['kol_vo'])) $error['error_empty'][] = array('id' => $id, 'error' => 'Количества');
            if (empty($val['price_purchase'])) $error['error_empty'][] = array('id' => $id, 'error' => 'Цены Закупки');
            if (empty($val['price'])) $error['error_empty'][] = array('id' => $id, 'error' => 'Стоимости');
            if (empty($val['price_deliver'])) $error['error_empty'][] = array('id' => $id, 'error' => 'Цены Доставки');
            if (empty($val['coefficient'])) $error['error_empty'][] = array('id' => $id, 'error' => 'Коэффициента');
            if (empty($val['price_sale'])) $error['error_empty'][] = array('id' => $id, 'error' => 'Рекомендуемой Цене');
        }

        //array_count_values - возвращает повторяющее элементы массива с их кол-вом
        $m = array_count_values($repeat);
        //вернуть мах значение или одно из значений, если их несколько
        //$m=array_keys($m);
        $key = array();
        foreach ($m as $id => $val) {
            if ($val >= 2) {
                // Обработка повторяющих "Артикулов" для вывода ошибки
                foreach ($repeat as $idi => $vals) {
                    if ($id == $vals) {
                        $error['error_repeat'][$idi] = $vals;
                    }
                }
            }
        }

        return $error;
    }

// ############# чтение excel файла #############
    function read_excel($dir)
    {
        require_once $_SERVER['DOCUMENT_ROOT'] . '/php_file/check_excel/excel_reader2.php';
        $data = new Spreadsheet_Excel_Reader();
        $data->setOutputEncoding('utf-8');
        $data->read($dir);
        return $data;
    }

// ############## Добавление новых данных в бд #############
    function record_date_bd($sql, $array)
    {
        global $db;
        //проверка какие переменные в массиве
        switch (count($array)) {
            // массив переменных  "Загруженый файл" - "array($dir,$name_file,date(),$id_users);"
            case 3:
                $ext = explode('/file/', $array[0]);
                $name = end($ext);
                $query = sprintf($sql, $array[0], $name, $array[1], $array[2]);
                break;
            // массив переменных "Поставщик" - "array($provider,date(),$id_users,$coment,$history);"
            case 6:
                $query = sprintf($sql, $array[0], '', '', '', '', $array[2], $array[1], $array[3], $array[4], $array[5]);
                break;
            // массив переменных  "Товар" - "$artikul,$name,$id_provider,$kol_vo,$price_purchase,$price_shipping,$coefficient,$price_sale,$date_add,$id_users,$coment,$history)"
            case 13:
                $query = sprintf($sql, $array[0], $array[1], $array[2], $array[3], $array[4], $array[5], $array[6], $array[7], '', $array[9], $array[8], $array[10], $array[11], $array[12]);
                break;
        }

        $result = mysqli_query($db, $query);
        if (mysqli_error($db)) {
            return false;
        }
        if (!$result) {
            return false;
        }
        //вернуть id добавленного элемента
        return mysqli_insert_id($db);

    }

// ############## Добавление новых данных в бд #############
    function delete_date_bd($sql, $array)
    {
        global $db;
        //проверка какие переменные в массиве
        switch (count($array)) {
            // массив переменных  "Загруженый файл" - "array($dir,$name_file,date(),$id_users);"
            case 1:
                $query = sprintf($sql, $array[0]);
                break;
        }
        $result = mysqli_query($db, $query);
        if ($result != 'true') {
            return ("not_delete_file");
        }
        return true;
    }

// ############## Обновление новых данных в бд Товары #############
    function update_date_bd($array, $id_users, $id_file, $inicial)
    {

        global $db;
        $amm = array();
        foreach ($array['update_comm'] as $id => $row) {
            /*
                *
                * 	Данные с Excel для обновления	* <<< ---
                *
            */

            $exl_artikul = $row['artikul'];                // Артикул
            $exl_name = $row['name'];                    // Название Товара
            $exl_provider = $row['provider'];                // Название Поставшика
            $exl_amount = (int)$row['kol_vo'];            // Кол-во Товаров
            $exl_price_purchase = number_format((float)$row['price_purchase'], 0, '.', '');// Цена закупки (за штуку)
            $exl_price = number_format((float)$row['price'], 0, '.', '');            // Стоимость (price_purchase * kol_vo)
            $exl_price_shipping = number_format((float)$row['price_deliver'], 0, '.', '');    // Стоимость Доставки  (за штуку)
            $exl_coefficient = (float)$row['coefficient'];    // Коэффициент Корреляции (Накрутка)
            $exl_price_sale = number_format((float)$row['price_sale'], 0, '.', '');    // Рекомендуемая Цена ((price_purchase + Доставки) * coefficient)  (за штуку)
            $exl_coment = $row['coment'];                // Комментарии к Товару

            /*
                *
                * 	Данные с Базы для обновления	** <<< ---
                *
            */
            $bd_bar_code = (int)$array['bd_comm'][$id]['bar_code'];        // Штрих-Код Товара
            $bd_artikul = $array['bd_comm'][$id]['id_article'];            // Артикул Товара
            $bd_name = $array['bd_comm'][$id]['name_commodity'];        // Название Товара
            $bd_provider = $array['bd_comm'][$id]['name_provider'];        // Название Поставщика
            $bd_amount = (int)$array['bd_comm'][$id]['amount'];            // Кол-во Товара
            $bd_price_purchase = number_format((float)$array['bd_comm'][$id]['price_purchase'], 0, '.', '');    // Цена закупки (за штуку)
            $bd_price_shipping = number_format((float)$array['bd_comm'][$id]['price_shipping'], 0, '.', '');    // Стоимость Доставки  (за штуку)
            $bd_coefficient = (float)$array['bd_comm'][$id]['coefficient'];    // Коэффициент Корреляции (Накрутка)
            $bd_price_sale = number_format((float)$array['bd_comm'][$id]['price_sale'], 0, '.', '');    // Рекомендуемая Цена ((price_purchase + Доставки) * coefficient)  (за штуку)
            $bd_photo_commodity = $array['bd_comm'][$id]['photo_commodity'];    // Фото Товара
            $bd_coment = $array['bd_comm'][$id]['coment_for_com'];        // Комментарии к Товару
            $bd_history_for_com = $array['bd_comm'][$id]['history_for_com'];    // История действий с Товарами
            $bd_id_file = (int)$array['bd_comm'][$id]['id_file'];        // ID файла Excel
            $bd_date_add = $array['bd_comm'][$id]['date_add'];            // Дата добавления Товара
            $bd_created_by = (int)$array['bd_comm'][$id]['created_by'];        // ID пользователя, который добавил товар

            /*
                *
                * 	Операции с полученными данными	** <<< ---
                *
            */
            $date_for_bd = '';
            $history = '';
            //if ($bd_amount != $exl_amount) 					$update_kol_vo			= $exl_amount+$bd_amount;	$date_for_bd .= "`amount`='".$update_kol_vo."',"; 					$history .= "\"Кол-во\", ";
            $date_for_bd .= "`amount`='" . ($exl_amount + $bd_amount) . "',";
            $history .= "\"Кол-во\", ";
            if ($bd_price_purchase != $exl_price_purchase) $date_for_bd .= "`price_purchase`='" . $exl_price_purchase . "',";
            $history .= "\"Стоимость Закупки\", ";
            if ($bd_price_shipping != $exl_price_shipping) $date_for_bd .= "`price_shipping`='" . $exl_price_shipping . "',";
            $history .= "\"Стоимость Доставки\", ";
            if ($bd_coefficient != $exl_coefficient) $date_for_bd .= "`coefficient`='" . $exl_coefficient . "',";
            $history .= "\"Коэффициент\", ";
            if ($bd_price_sale != $exl_price_sale) $date_for_bd .= "`price_sale`='" . $exl_price_sale . "',";
            $history .= "\"Цена Реализации\", ";

            // История действий пользователя по товару
            $new_history = " " . date('d-m-Y G:i:s') . ">>" . $inicial . ">> Обновлены следующие Позиции \"Товара\": " . mb_substr($history, 0, -2) . " ||";
            $all_history = $bd_history_for_com . $new_history;
            //проверка чтоб история не привышала 65535 символов, т.к. ячейка в MYSQL типом: text
            if (strlen($all_history) >= 65535) {
                $all_history = $new_history;
            }
            $sql = "UPDATE `commodity` SET $date_for_bd `created_by`='%d',`date_add`='%s',`history_for_com`='%s',`id_file`='%d'	WHERE	bar_code = '%d'	";
            $query = sprintf($sql, $id_users, date('Y-m-d G:i:s'), $all_history, $id_file, $bd_bar_code);
            $result = mysqli_query($db, $query);
            if (mysqli_error($db)) {
                $amm['error_update'][$id] = $bd_bar_code;
            }
            if (!$result) {
                $amm['error_update'][$id] = $bd_bar_code;
            } else {
                $amm['true_update'][$id] = $bd_bar_code;
            }

        }

        if (is_array($amm['error_update'])) {

            if (is_array($amm['true_update'])) {
                foreach ($amm['true_update'] as $id => $row) {

                    /*
                        *
                        * 	Данные с Базы для отмены изменении	** <<< ---
                        *
                    */
                    $bd_bar_code = (int)$array['bd_comm'][$id]['bar_code'];        // Штрих-Код Товара
                    $bd_artikul = $array['bd_comm'][$id]['id_article'];            // Артикул Товара
                    $bd_name = $array['bd_comm'][$id]['name_commodity'];        // Название Товара
                    $bd_provider = $array['bd_comm'][$id]['name_provider'];        // Название Поставщика
                    $bd_amount = (int)$array['bd_comm'][$id]['amount'];            // Кол-во Товара
                    $bd_price_purchase = number_format((float)$array['bd_comm'][$id]['price_purchase'], 0, '.', '');    // Цена закупки (за штуку)
                    $bd_price_shipping = number_format((float)$array['bd_comm'][$id]['price_shipping'], 0, '.', '');    // Стоимость Доставки  (за штуку)
                    $bd_coefficient = (float)$array['bd_comm'][$id]['coefficient'];    // Коэффициент Корреляции (Накрутка)
                    $bd_price_sale = number_format((float)$array['bd_comm'][$id]['price_sale'], 0, '.', '');    // Рекомендуемая Цена ((price_purchase + Доставки) * coefficient)  (за штуку)
                    $bd_photo_commodity = $array['bd_comm'][$id]['photo_commodity'];    // Фото Товара
                    $bd_coment = $array['bd_comm'][$id]['coment_for_com'];        // Комментарии к Товару
                    $bd_history_for_com = $array['bd_comm'][$id]['history_for_com'];    // История действий с Товарами
                    $bd_id_file = (int)$array['bd_comm'][$id]['id_file'];        // ID файла Excel
                    $bd_date_add = $array['bd_comm'][$id]['date_add'];            // Дата добавления Товара
                    $bd_created_by = (int)$array['bd_comm'][$id]['created_by'];        // ID пользователя, который добавил товар

                    // История действий пользователя по товару
                    $new_history = " " . date('d-m-Y G:i:s') . ">>" . $inicial . ">>Автоматическая Отмена последних обновлений ||";
                    $all_history = $bd_history_for_com . $new_history;
                    $date_for_bd = "`amount`='" . $bd_amount . "',`price_purchase`='" . $bd_price_purchase . "',`price_shipping`='" . $bd_price_shipping . "',`coefficient`='" . $bd_coefficient . "',`price_sale`='" . $bd_price_sale . "',";
                    //проверка чтоб история не привышала 65535 символов, т.к. ячейка в MYSQL типом: text
                    if (strlen($all_history) >= 65535) {
                        $all_history = $new_history;
                    }
                    $sql = "UPDATE `commodity` SET $date_for_bd `created_by`='%d',`date_add`='%s',`history_for_com`='%s',`id_file`='%d'	WHERE	bar_code = '%d'	";
                    $query = sprintf($sql, $bd_created_by, date('Y-m-d G:i:s'), $all_history, $id_file, $bd_bar_code);
                    $result = mysqli_query($db, $query);
                    if (!$result) {
                        return false;
                    }

                }

            }

            return $amm['error_update'];
        }
        if (is_array($amm['true_update']) && count($amm['true_update']) == count($array['update_comm'])) {
            return $amm;
        }
    }

// ############## очистка всех новых товаров загруженных в бд #############
    function delete_new_com($array)
    {
        $sql_del_prov = "DELETE	FROM `commodity` WHERE id_article = '%d' ";
        if (is_array($array)) {
            $m = array();
            foreach ($array as $id => $val) {
                $rec_commodity = array($id);
                if (delete_date_bd($sql_del_prov, $rec_commodity)) {
                    $m['prv_del'][$id] = $val;
                } else {
                    $m['prv_error'][$id] = $val;
                }

            }
            return $m;
        } else {
            return false;
        }

    }

// ############## Проверка существования Данных в бд #############
    function check_date_bd($sql, $array)
    {
        global $db;
        //проверка какие переменные в массиве
        switch (count($array)) {
            // массив переменных "array($provider)"
            case 1:
                $query = sprintf($sql, $array[0]);
                break;
            // массив переменных "array($artikul,$name,$provider)"
            case 2:
                $query = sprintf($sql, $array[0], $array[1]);
                break;
        }
        $result = mysqli_query($db, $query);
        if (mysqli_error($db)) {
            return ("syntax");
        }
        if (mysqli_num_rows($result) == 0) {
            return ('not_find_data');
        }
        $row = array();
        for ($i = 0; $i < mysqli_num_rows($result); $i++) {
            $row[] = mysqli_fetch_array($result, MYSQL_ASSOC);
        }
        return $row;
    }

// ############## Функция сравнения базы Товаров и товары из Excel #############	
    function rec_com($array, $sql_com)
    {

        if (is_array($array)) {
            // # массив с данными
            $com = array();

            foreach ($array as $id => $row) {

//					# * Действия с переменными * # <<<<<<-----
                $artikul = escape(htmlspecialchars($row['artikul'], ENT_QUOTES));        //Артикул
                $provider = escape(htmlspecialchars($row['provider'], ENT_QUOTES));        //Название поставщика
//					# * Действия с переменными * # <<<<<<-----

// запись переменных в массив для "Товаров"
                $perem_commodity = array($artikul, $provider);
// Проверка существования "Товара"
                $check_commodity = check_date_bd($sql_com, $perem_commodity);

                switch ($check_commodity) {
                    case 'error_bd_commodity'         :
                        return false;
                        break;
                    case 'error_bd_syntax_commodity' :
                        return false;
                        break;
                    case 'not_find_data'             :
                        $com['new_comm'][$id] = $row;
                        break;
                    default:
                        $com['update_comm'][$id] = $row;
                        $com['bd_comm'][$id] = $check_commodity[0];
                        break;
                }

            }
            return $com;
        } else {
            return false;
        }

    }

// ############## Функция Записи всех поставщиков #############
    function rec_prov($arr, $sql_search_prov, $sql_add_prov, $id_users, $inicial, $id_file)
    {
        // поиск совпадений в массиве
        $prov = prov($arr);
        //массив для новых поставщиков
        $new_prov = array();
        foreach ($prov as $val) {
            // запись переменных в массив для "Поставшика"
            $perem_provid = array($val);
            //print_r($perem_provid);
            // Проверка существования "Поставщика"
            $check_provid = check_date_bd($sql_search_prov, $perem_provid);
            //вернуть № поставщика в системе
            switch ($check_provid) {
                case "not_find_data":
                    $history = "|| " . date('d-m-Y G:i:s') . ">>" . $inicial . ">> Поставщик Загружен с Excel ||";
                    $rec_provider = array($val, date('Y-m-j G:i:s'), $id_users, '', $history, $id_file);
                    $id_provider = record_date_bd($sql_add_prov, $rec_provider);
                    $new_prov['new_prov'][$id_provider] = $val;
                    $new_prov['all_prov'][$id_provider] = $val;
                    break;
                case "not_find_table":
                    return false;
                    break;
                case "syntax":
                    return false;
                    break;
                default:
                    $id_prov = $check_provid[0]['id_provider'];
                    $nam_prov = escape(htmlspecialchars($check_provid[0]['names'], ENT_QUOTES));
                    $new_prov['old_prov'][$id_prov] = $nam_prov;
                    $new_prov['all_prov'][$id_prov] = $nam_prov;
                    break;
            }
        }
        return $new_prov;
    }

// ############## Проверка массива на повторяющее значение "Поставшика" #############
    function prov($ms)
    {

        $prov = array();
        //создание массива с "Поставшиками"
        foreach ($ms as $row) {
            $prov[] = escape(htmlspecialchars($row['provider'], ENT_QUOTES));
        }
        //удалить пустые значения из массива
        $prov = array_diff($prov, array(''));
        //array_count_values - возвращает повторяющее элементы массива с их кол-вом
        $prov = array_count_values($prov);
        //вернуть мах значение или одно из значений, если их несколько
        $prov = array_keys($prov);
        return $prov;
    }

// ############## Запись в бд #############
    function input_bd($array, $id_add_file)
    {


//номер пользователя
        $id_users = $_SESSION['id_users'];
//Инициалы пользователя
        $inicial = $_SESSION['inicial'];
        $new_comm = array();    // массив с новыми товарами
        $new_prov = array();    // массив с новыми поставщиками
        $update_comm = array();     // масив с обновленными данными
        $error_comm = array();     // масив с ошибками
//// ************ SQL запросы **********
        $sql_for_commodity = "
	select 
					bar_code,
					id_article,
					name_commodity,
					provider.names as name_provider,
					amount,
					price_purchase,
					price_shipping,
					coefficient,
					price_sale,
					photo_commodity,
					commodity.created_by,
					commodity.date_add,
					coment_for_com,
					history_for_com,
					commodity.id_file
					 
			from `commodity` 
			left join `provider` on (`commodity`.`provisioner`=`provider`.`id_provider`)
			left join `users` on (`provider`.`created_by`=`users`.`id_users`)
			where
			id_article 		= '%s' and
			provider.names 	= '%s'
			";

        $sql_add_commodity = "
	INSERT INTO 
		commodity
		(
				bar_code,
				id_article,
				name_commodity,
				provisioner,
				amount,
				price_purchase,
				price_shipping,
				coefficient,
				price_sale,
				photo_commodity,
				created_by,
				date_add,
				coment_for_com,
				history_for_com,
				id_file
		)
	VALUES (
				NULL,
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
				'%d',
				'%s',
				'%s',
				'%s',
				'%d'
			)";

        $sql_for_provider = "
	select 
					id_provider,
					provider.names,
					provider.email,
					provider.number_phone,
					adr_providers,
					url_providers,
					created_by,
					date_add,
					coment_for_pro,
					history_for_pro,
					id_file
					
			FROM `provider`
			left join `users` on (`provider`.`created_by`=`users`.`id_users`)
			where
			`provider`.`names` = '%s'
			";

        $sql_add_provider = "
	INSERT INTO 
		provider(
			id_provider,
			names,
			email,
			number_phone,
			adr_providers,
			url_providers,
			created_by,
			date_add,
			coment_for_pro,
			history_for_pro,
			id_file
			) 
	VALUES 
		(
			NULL,
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d'
		)";

//// ************ конец **********

        // проверка и запись нового поставщика
        $provider_all = rec_prov($array, $sql_for_provider, $sql_add_provider, $id_users, $inicial, $id_add_file);
        if (!$provider_all) {
            return 'error_bd_provider';
        }
        // проверка записи нового товара
        $check_com = rec_com($array, $sql_for_commodity);
        if (!$check_com) {
            delete_new_providers($provider_all['new_prov']);
            return 'error_bd_commodity';
        }

        /*
                *
                *	Проверка есть ли в массиве товары для "Добавления"
                *
        */
        if (is_array($check_com['new_comm'])) {

            foreach ($check_com['new_comm'] as $id => $row) {

                //					# * Действия с переменными * # <<<<<<-----
                $artikul = escape(htmlspecialchars($row['artikul'], ENT_QUOTES));        //Артикул
                $name = escape(htmlspecialchars($row['name'], ENT_QUOTES));            //Название
                $provider = escape(htmlspecialchars($row['provider'], ENT_QUOTES));        //Название поставщика
                $kol_vo = (int)escape(htmlspecialchars($row['kol_vo'], ENT_QUOTES));            //Кол-во
                $price_purchase = number_format((float)escape(htmlspecialchars($row['price_purchase'], ENT_QUOTES)), 0, '.', ''); //Стоимость закупки
                $price = number_format((float)escape(htmlspecialchars($row['price'], ENT_QUOTES)), 0, '.', '');            //Стоимость
                $price_shipping = number_format((float)escape(htmlspecialchars($row['price_deliver'], ENT_QUOTES)), 0, '.', '');    //Стоимости доставки
                $coefficient = (float)escape(htmlspecialchars($row['coefficient'], ENT_QUOTES));    //Коэффициент
                $price_sale = number_format((float)escape(htmlspecialchars($row['price_sale'], ENT_QUOTES)), 0, '.', '');        //Рекомендуемая цена
                $coment = escape(htmlspecialchars($row['coment'], ENT_QUOTES));            //Комментарии
                $id_provid = array_search($provider, $provider_all['all_prov']);            //Поиск "Поставщиков" по массиву
                //					# * Действия с переменными * # <<<<<<-----

                // запись нового товара
                $history = "|| " . date('d-m-Y G:i:s') . ">>" . $inicial . ">> Товар Загружен с Excel ||";
                $rec_commodity = array($artikul, $name, $id_provid, $kol_vo, $price_purchase, $price_shipping, $coefficient, $price_sale, date('Y-m-j G:i:s'), $id_users, $coment, $history, $id_add_file);
                $rec = record_date_bd($sql_add_commodity, $rec_commodity);

                // проверка, есть ли ошибка при записи в базу "Товаров"
                if (!$rec) {
                    $error_comm['error_add_com'][$id] = $artikul;
                }
                $new_comm[$id] = $artikul;

            }
        }
        /*
                *
                *	Проверка есть ли в массиве товары для "Обновления"
                *
        */
        if (is_array($check_com['update_comm'])) {

            $up = update_date_bd($check_com, $id_users, $id_add_file, $inicial);
            if (!$up) {
                $error_comm['error_update_com'] = 'error_update_com';
            }
            if (is_array($up['error_update'])) {
                $error_comm['error_update_com'] = 'error_update_com';
            }
            $update_comm = $up['true_update'];

        }
        $ms = '';
        if (!empty($new_comm)) {
            $ms .= 'Добавлено <b>' . count($new_comm) . '</b> "Товар(ов)"<br>';
        }

        if (!empty($provider_all['new_prov'])) {
            $ms .= 'Добавлено <b>' . count($provider_all['new_prov']) . '</b> "Поставщик(ов)"<br>';
        }

        if (!empty($update_comm)) {
            $ms .= 'Обновлена <b>' . count($update_comm) . '</b> Позиция(и) с "Товарами"<br>';
        }

        if (!empty($ms)) {
            return $ms;
        } else {
            return 'not_find_for_bd';
        }
    }


    // ##########################################  Основной код #######################################
    $file = htmlspecialchars($_POST['files_names'], ENT_QUOTES);
    $puti = $_SERVER['DOCUMENT_ROOT'] . $file;

    if (file_exists($puti)) {

        $data_exel = read_excel($puti);
        $name_template_exel = stripslashes(htmlspecialchars($data_exel->val(2, 'B', 0)));    // название шаблона
        $number_template_exel = stripslashes(htmlspecialchars($data_exel->val(2, 'D', 0)));    // № шаблона
        $type_EUR = '1.1.2';    // # шаблон с Евро просчетом
        $type_RUB = '2.0.2';    // # шаблон с Руб. просчетом
        switch ($number_template_exel) {
            case $number_template_exel == $type_EUR :

                $type = '1';
                $kol_cols = stripslashes(htmlspecialchars($data_exel->val(12, 'M', 0)));
                break;

            case $number_template_exel == $type_RUB :

                // данные о поставщике
                $comp_name = untab(stripslashes(htmlspecialchars($data_exel->val(2, 'F', 0))));
                $comp_adr = untab(stripslashes(htmlspecialchars($data_exel->val(3, 'F', 0))));
                $comp_url = untab(stripslashes(htmlspecialchars($data_exel->val(4, 'F', 0))));
                $comp_nmb = untab(stripslashes(htmlspecialchars($data_exel->val(5, 'F', 0))));
                $comp_email = untab(stripslashes(htmlspecialchars($data_exel->val(6, 'F', 0))));
                $type = '2';
                $kol_cols = stripslashes(htmlspecialchars($data_exel->val(12, 'L', 0)));
                break;
            default:
                unlink_all_file($dir);
                exit('Not is the installation version');
                break;
        }
        $kol_str = stripslashes(htmlspecialchars($data_exel->val(113, 'C', 0)));

        $sum_str = ($kol_str + 12);
        $sum_cols = ($kol_cols + 1);
        $number_spec = stripslashes(htmlspecialchars($data_exel->val(3, 'D', 0)));
        $data_create = stripslashes(htmlspecialchars($data_exel->val(4, 'D', 0)));
        $company = untab(stripslashes(htmlspecialchars($data_exel->val(5, 'D', 0))));
        $user = untab(stripslashes(htmlspecialchars($data_exel->val(6, 'D', 0))));
        $tel = untab(stripslashes(htmlspecialchars($data_exel->val(7, 'D', 0))));
        $email = untab(stripslashes(htmlspecialchars($data_exel->val(8, 'D', 0))));
        $result = output_excel($sum_str, $sum_cols, $data_exel, $type);
        $each_er = error_result_excel($result);
        //print_r($each_er);
        //exit;
        if (empty($each_er)) {
            mb_internal_encoding("UTF-8");
            require_once "bd.php";
            $sql_add_file = "
	INSERT INTO
			upload_file
					(
					id_file,
					path,
					name_file,
					date_add,
					created_by
					)
	VALUES 
				(
					NULL,
					'%s',
					'%s',
					'%s',
					'%d'
				)";

            $sql_del_file = "
	DELETE 
		FROM 
				`upload_file`
		WHERE
				id_file = '%d'";
            // Запись ссылки загруженного файла
            $rec_provider = array($file, date('Y-m-d G:i:s'), $_SESSION['id_users']);
            $id_add_file = record_date_bd($sql_add_file, $rec_provider);
            $d = input_bd($result, $id_add_file);
            switch (mb_substr($d, 0, 13)) {
                case 'Добавлено <b>' :
                    $m = $d;
                    break;
                case 'Обновлена <b>' :
                    $m = $d;
                    break;
                default :
                    echo '<b>Возникла непредвиденная ошибка<b>';
                    $rec_commodity = array($id_add_file);
                    delete_date_bd($sql_del_file, $rec_commodity);
                    unlink_all_file($puti);
                    break;
            }
            if (isset($m)) {
                echo '<h3>В результате обработки спецификации:</h3>' . $m;
            }
            mysqli_close($db);
        } else {
            // обработка ошибок на пустые ячейки
            if (!empty($each_er['error_empty'])) {
                echo '<h3>Найдены следующие ошибки в спецификации:</h3>';
                foreach ($each_er['error_empty'] as $val) {
                    echo 'Позиция: <b>' . $val['id'] . '</b> Ошибка в Отсутствии <b>' . $val['error'] . '</b> в ячейке<br>';
                }
                unlink_all_file($puti);
                echo '<br><left><b>РЕШЕНИЕ:</b></left><li>Исправте найденые ошибки;</li><li>Снова загрузите исправленный документ.</li>';
                exit;
            }
            // обработка ощибок на повторяющее значения
            if (!empty($each_er['error_repeat'])) {
                echo '<h3> Найдены следующие ошибки в спецификации:</h3> <br> Обнаружены повторяющие Артикулы в позициях ';
                $i = 0;
                $col = count($each_er['error_repeat']);
                foreach ($each_er['error_repeat'] as $id => $val) {
                    $i++;
                    echo '<b>' . $id . '</b>';
                    if ($i < $col) {
                        echo ',';
                    }
                }
                unlink_all_file($puti);
                echo '<br><br><left><b>РЕШЕНИЕ:</b></left><li>Исправте найденые ошибки;</li><li>Снова загрузите исправленный документ.</li>';
                exit;
            }
        }
    } else {
        echo 'no file';
    }

} else {
    header("Location: ./../index.php");
}
?>
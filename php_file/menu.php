<?php if ($_SERVER["SCRIPT_NAME"] != '/php_file/menu.php' && preg_match('/php/i', $_SERVER['SCRIPT_NAME'])) : ?>
    <html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <link type="text/css" rel="stylesheet" href="../css/config.css"/>
        <!-- Основные стили проекта -->
        <link type="text/css" rel="stylesheet" href="../css/config_tips.css"/>
        <!-- стили для блока всплывающих подсказок -->
        <link type="text/css" rel="stylesheet" href="../css/jquery.alerts.css"/>
        <!-- стили для jAlert, jConfirm, jPrompt - сообщения -->
        <link type="text/css" rel="stylesheet" href="../css/jqueryui.custom.css"/>
        <!-- стили для dialog({}); диалоговые сообщения -->
        <link type="text/css" rel="stylesheet" href="../css/config_tabl_style.css"/>
        <!-- стили для таблиц -->
        <link type="text/css" rel="stylesheet" href="../css/jPaginator.css"/>
        <!-- стили для построчной навигации -->
        <link type="text/css" rel="stylesheet" href="../css/jquery.upload.css"/>
        <!-- стили для кнопки загрузки-->
        <link type="text/css" rel="stylesheet" href="../css/config_for_shoping_cart.css"/>
        <!-- стили для кнопки корзины -->
        <link type="text/css" rel="stylesheet" href="../css/config_section.css"/>
        <!-- стили для кнопки выбора -->
        <link type="text/css" rel="stylesheet" href="../css/config_checkbox.css"/>
        <!-- стили для кнопки checkbox -->
        <link type="text/css" rel="stylesheet" href="../css/config_calendar_style.css"/>
        <!-- стили для каледаря -->

        <script type="text/javascript" src="../script/jquery.min.js"></script>
        <!-- Основная библиотета jquery 1.4.2 -->
        <script type="text/javascript" src="../script/jquery.alerts.js"></script>
        <!-- скрипт для jAlert, jConfirm, jPrompt - сообщения -->
        <script type="text/javascript" src="../script/script_pockazka.js"></script>
        <!-- скрипт для блока всплывающих подсказок -->
        <script type="text/javascript" src="../script/jqueryui.custom.js"></script>
        <!-- скрипт для dialog({}); диалоговые сообщения -->
        <script type="text/javascript" src="../script/jPaginator-min.js"></script>
        <!-- скрипт для построчной навигации-->
        <script type="text/javascript" src="../script/upload_swf.js"></script>
        <!-- скрипт для построчной навигации-->
        <script type="text/javascript" src="../script/upload_script.js"></script>
        <!-- скрипт для построчной навигации-->
        <script type="text/javascript" src="../script/jquery.mousewheel.js"></script>
        <!-- скрипт для календаря сисстемы-->
        <script type="text/javascript" src="../script/calendar.js"></script>
        <!-- скрипт для календаря сисстемы-->
        <!--
        <script type="text/javascript" 						src="../script/jquery.session.js"></script> 	<!-- скрипт для работы сессией-->
        <!--<script type="text/javascript" 						src="http://jquery.page2page.ru/plagins/textareaAutoresize/autoresize.jquery.min.js"></script> 	<!-- скрипт для построчной навигации-->
    </head>

    <!-- div 'Элемент' для демонстрации корзины-->
    <div class="shop_container">
        <div class="shop_content">
            <div class="button-wrapper-large">
                <a href="#" class="a-btn" onclick="active_shoping_cart();">
                    <span class="a-btn-text">В Корзине <b id="sh_amount">(0)</b></span>
                    <span class="a-btn-slide-text">На сумму: <br><b id="sh_sum" style="color:red;">0</b> руб.</span>
                    <span class="a-btn-icon-right"><span></span></span>
                </a>
            </div>
        </div>
    </div>
    <!-- div 'Элемент' для демонстрации корзины-->

    <?php

    // выборка из базы роли пользователя
    //$s = mysql_fetch_array(mysql_query("SELECT `users`.`id_users`,`users`.`names`,`users`.`surnames`,`users`.`patronymic`,`functions`.`names` FROM `users`,`functions`  where `login`='$login' and `password` = '$password' and `users`.`function` = `functions`.`id_function`", $db));
    //$role = iconv("utf-8","cp1251",htmlspecialchars($s[1]));
    $dolz = $_SESSION['function'];
    $inicial = $_SESSION['inicial'];
    //print_r($_SESSION);
    //$dolz = iconv("utf-8","cp1251",htmlspecialchars($s[4]));
    /*проверка роли пользователя и раздача определенных прав*/
    //if($role=="admin"){$rol=""; session_register('role');}else{$rol="display:none";}

    ?>

    <script type="text/javascript">

        ///							### Запоминать Скрол страницы при обновлениии ###
        var html = document.documentElement;
        var body = document.body;
        var scrollTop = html.scrollTop || body && body.scrollTop || 0;

        var contentold = {};   //объявляем переменную для хранения неизменного текста в тегах contenteditable = "true"

        scrollTop -= html.clientTop;
        //alert("Текущая прокрутка: " + scrollTop);

        var getPageScroll = (window.pageXOffset != undefined) ?
            function () {
                return {
                    left: pageXOffset,
                    top: pageYOffset
                };
            } :
            function () {
                var html = document.documentElement;
                var body = document.body;

                var top = html.scrollTop || body && body.scrollTop || 0;
                top -= html.clientTop;

                var left = html.scrollLeft || body && body.scrollLeft || 0;
                left -= html.clientLeft;

                return {top: top, left: left};
            }
        ///							### Конец ###

        // #### функция демонстрации выполнения ajax скрипта ###
        $(document).ajaxStart(function () {
            jQuery('body').append('<div class="fon_alert_loader" style="position:absolute;z-index:1999;"></div>');  	//создание div для заливки фона
            jQuery('body').append('<div class="container"><div class="content"><div class="circle"></div><div class="circle1"></div></div></div>');  	//создание div для заливки фона
            jQuery('.fon_alert_loader').fadeIn(300);
            $('.container').show();
        }).ajaxStop(function () {
            $('.container').hide();
            $('.fon_alert_loader').remove();
            $('.container').remove();
        });

        function numFormat(n, d, s) { // number format

            if (arguments.length == 2) {
                s = " ";
            }

            if (arguments.length == 1) {
                s = " ";
                d = ".";
            }
            n = n.toString();
            a = n.split(d);
            x = a[0];
            y = a[1];
            z = "";

            if (typeof(x) != "undefined") {

                for (i = x.length - 1; i >= 0; i--) z += x.charAt(i);
                z = z.replace(/(\d{3})/g, "$1" + s);
                if (z.slice(-s.length) == s) z = z.slice(0, -s.length);
                x = "";
                for (i = z.length - 1; i >= 0; i--) x += z.charAt(i);
                if (typeof(y) != "undefined" && y.length > 0) x += d + y;

            }

            return x;
        }

        // #### функция вывода количества и итоговой суммы в корзине пользователя ###
        chec_shoping_cart();

        function chec_shoping_cart() {

            ASPSESSID = "$2a$10$1g$fImZBGCCioFjopWCR2O3haz2UD0D.LVKRK.61myCItfa.uv/De";
            var sum = 0;
            var kol = 0;

            $.ajax({
                url: "check_input.php",
                dataType: "json",
                type: "POST",
                data: "asps=" + ASPSESSID,
                async: false,
                cache: false,
                success: function (res) {
                    sum = res.result.summa[0];
                    kol = res.result.kol[0];
                }
            });
            $('#sh_sum').text(sum);
            $('#sh_amount').text('(' + kol + ')');
        }
        // #### функция вывода кнопки выбора для section ###
        function show_section(id) {
            function DropDown(el) {
                this.dd = el;
                this.placeholder = this.dd.children('span');
                this.opts = this.dd.find('ul.dropdown > li');
                this.val = '';
                this.index = -1;
                this.initEvents();
            }

            DropDown.prototype = {
                initEvents: function () {
                    var obj = this;

                    obj.dd.on('click', function (event) {
                        $(this).toggleClass('active');
                        return false;
                    });

                    obj.opts.on('click', function () {
                        var opt = $(this);
                        id = opt.find('a').attr('id');
                        obj.val = opt.text();
                        obj.index = opt.index();
                        obj.placeholder.text(obj.val).attr('id', id);
                    });
                },
                getValue: function () {
                    return this.val;
                },
                getIndex: function () {
                    return this.index;
                }
            }

            $(function () {

                var dd = new DropDown($(id));

                $(document).click(function () {

                    // all dropdowns
                    $('.wrapper-dropdown-3').removeClass('active');
                });

            });
        }
        ;
        // #### функция вывода поробной корзины с возможность изменения ###
        function sample_shoping() {
            var ASPSESSID = "$2a$10$1g$fImZBGCCioFjopWCR2O3haz2UD0D.LVKRK.61myCItfa.uv/De", srt_tel = 0;

            $.ajax({
                url: "check_input.php",
                dataType: "json",
                type: "POST",
                data: "asps=" + ASPSESSID,
                async: false,
                cache: false,
                success: function (res) {
                    //alert(res);

                    if (res != 'undefined') {
                        // шапка таблицы
                        srt_tel = "<table class='deist' border='0' cellspacing='0'><tr id='shapka'><th>Артикул</th><th>Наименование</th><th>Цена, руб.</th><th>Кол-во</th><th>Сумма, руб.</th><th><span class='quantity'><a class='button liteblue' onclick='dell_shop(\"del_all_commodity\");'>x</a></span></th></tr>";
                        sum = res.result.summa[0];
                        kol = res.result.kol[0];

                        br = res.date.bar_code;
                        ar = res.date.id_article;
                        nm = res.date.name;
                        am = res.date.amount;
                        mx = res.date.max;
                        pr = res.date.price;
                        tl = res.date.in_total;
                        // тело таблицы
                        for (var key in br) {
                            srt_tel += "<tr class='tel_tabl'><td class='bar_code' id='" + br[key] + "'>" + ar[key] + "</td><td class='name_com'>" + nm[key] + "</td><td id='" + pr[key].replace(' ', '') + "'>" + pr[key] + "</td><td><span class='quantity'><a class='button liteblue btn_cart_delete' onclick='addition_shop(" + parseInt(br[key]) + ",\"remove\"," + parseInt(mx[key]) + ");'>-</a><input value='" + parseInt(am[key]) + "' id='" + parseInt(am[key]) + "' class='cart_qty' maxlength='2' onKeyPress ='if (((event.keyCode < 48) || (event.keyCode > 57))&&(event.keyCode != 46)) event.returnValue = false;' onBlur='addition_shop(" + parseInt(br[key]) + ",\"blur_input\"," + parseInt(mx[key]) + ")' /><a class='button liteblue btn_cart_add' onclick='addition_shop(" + parseInt(br[key]) + ",\"adding\"," + parseInt(mx[key]) + ");'>+</a></span></td><td id='" + tl[key].replace(' ', '') + "'>" + tl[key] + "</td><td><span class='quantity'><a class='button liteblue' onclick='dell_shop(" + parseInt(br[key]) + ");'>x</a></span></td></tr>";
                        }
                        // низ таблицы
                        srt_tel += "<tr class='sum' style='font-weight: 600;'><td  colspan='4' style='text-align:right;'>Итого:</td><td id='" + sum.replace(' ', '') + "'>" + sum + "</td><td></td></tr></table>";
                    }

                }

            });
            return srt_tel;

        }

        // #### функция вывода чека пользователя ###
        function show_check() {
            id = '<?php echo $_SESSION['id_users'];?>';
            chat = window.open('check_pdf/temp_pdf/report-' + id + '.pdf', '_blank', 'toolbar=0, location=0, directories=0, status=0, menubar=0, scrollbars=0, resizable=0, width=820px, height=600px');
            chat.focus();
        }
        // #### функция вывода чека пользователя ###
        function creat_check_pdf(check) {

            ASPSESSID = "$2a$10$1g$IFNtD0o6E2xAoJjvLI.grgsaIalP/c7KN5MsylGPO/UNL94xSW";
            $.ajax({
                url: "indx.php",
                type: "POST",
                data: "cech=" + ASPSESSID + "&id_check=" + check,
                async: true,
                cache: false,
                success: function (red) {
                    if (red == 'ok') {
                        show_check();
                    } else {
                        jAlert_add('<h3>Возникла непредвиденная ошибка при обработки чека!</h3>');
                    }
                }
            });
        }

        // #### Функция Возврата товара на склад ###
        function return_check(check) {
            alert_show();
            jQuery.alerts.okButton = '&nbsp;Да&nbsp;';
            jQuery.alerts.cancelButton = '&nbsp;Нет&nbsp;';
            jConfirm("<h2 style='font-size:18px;color:red;'>Выполнить возврат товара?</h2>", ' ', function (r) {
                if (r == true) {
                    ASPSESSID = "$2a$10$1g$CdihywT1Ysw4O7SzY4ucel4MBck0Ls1j4yt2IvT6bTyMYUYYoK";
                    $.ajax({
                        url: "check_input.php",
                        type: "POST",
                        data: "cech=" + ASPSESSID + "&id_check=" + check,
                        async: true,
                        cache: false,
                        success: function (red) {

                            switch (red) {
                                case 'ok' :
                                    str_amount = $("select#selec_element").val();
                                    data_mess = error_find_bd(selection_bd(str_amount, str_num, sorting, bd_name, table_name), no_data);
                                    $('tbody#top').html(data_mess);
                                    break;
                                case 'error' :
                                    jAlert_add('<h3>Возникла непредвиденная ошибка при возврате чека!</h3>');
                                    break;
                                case 'error_dip_access' :
                                    jAlert_add('<h3>У вас нехватает прав для выполнения данного действия!</h3>');
                                    break;
                            }

                        }
                    });

                }
                jQuery('body').css("overflow", "visible");
                jQuery('.fon_alert').remove();
            });
        }
        // #### функция вывода поробной корзины с возможность изменения ###
        function active_shoping_cart() {
            var  //переменные с данными
                bt_cashier, bt_contractor, bt_type_of_tax, bt_certificate, bt_sale, bt_coment, kalendar = '';
            // Подробная форма корзины
            srt_te = sample_shoping();
            // показать кнопку закрытие
            $('.ui-dialog-titlebar-close').show();
// проверка есть ли данные в подробной корзине
            if (srt_te == 0) {
                if ($('b#sh_amount').text() != '(0)')location.reload();
                srt_te = 'Ошибка загрузки корзины';
            }

            // сообщение с загрузкой файла
            mess = srt_te;
            add_dialog_message(mess, 400, 750, false, 'Подробный Счет на оплату');
            //удалить кнопку перехода
            if ($('#dialog-message').text() == srt_te) {
                $('.ui-button').remove();
            }
            $('span.ui-button-text').attr('id', 'input_date');
            // дейстиве при нажатии на кнопку "Далее"
            $('span.ui-button-text').bind('click', function () {
                switch ($(this).attr('id')) {

                    case 'input_date' :


                        var
                        //данные для изменения кнопки
                            bt_sh1, bt_sh2, bt_sh3, bt_sh4, chb_1 = 1, chb_2 = 1, kal, sum = $('.sum').closest('tr').find('td').eq(1).attr('id');

                        kathir = '			<section class="main">	<div class="wrapper-demo"><div id="sh1" class="wrapper-dropdown-3" tabindex="1"><span>Кассир</span><ul class="dropdown">' + selection_kathir('kathir') + '</ul></div></section>';
                        contractor = '		<section class="main">	<div class="wrapper-demo"><div id="sh2" class="wrapper-dropdown-3" tabindex="1"><span>Контрагент</span><ul class="dropdown">' + selection_kathir('contractor') + '	</ul></div></section>';
                        type_calculation = '	<section class="main">	<div class="wrapper-demo"><div id="sh3" class="wrapper-dropdown-3" tabindex="1"><span>Тип Расчета</span><ul class="dropdown"><li><a href="#" id="0">Наличный</a></li><li><a href="#" id="1">Безналичный</a></li><li><a href="#" id="2">Сертификат</a></li><li><a href="#" id="3">Брак</a></li></ul></div></section>';
                        sale = '				<input type="checkbox" class="checkbox" id="checkbox-1" /><label for="checkbox-1">Скидка</label>';
                        coment = '			<input type="checkbox" class="checkbox" id="checkbox-2" /><label for="checkbox-2">Комментарий</label>';
// Скрыть кнопку
                        $('.ui-button').hide();
                        // Пример формы
                        if (check_role_in_system() == 'Администратор' || check_role_in_system() == 'Директор') kalendar = ' <input type="text" id="calendar" size="11" readonly style="display: inline-block; text-align:center;" id="undefined_display" placeholder="Выберете дату..."/><br><center style="margin-top: 2%;"> </center>';
                        mess = '<div style="text-align:center;"><h3 >Выберите и введите необходимые данные: </h3><br>' + kalendar + kathir + contractor + type_calculation + '<br>' + sale + '<br><br>' + coment + ' <div>';
// вывести форму интерфейса для пользователя
                        $('#dialog-message').html(mess);


                        $('input#calendar').will_pickdate({
                            format: 'Y-m-j H:i',
                            inputOutputFormat: 'Y-m-d H:i:s',
                            days: ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'],
                            months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
                            timePicker: true,
                            militaryTime: true,
                            allowEmpty: true,
                            yearsPerPage: 3,
                            allowEmpty: true
                        });


// вывести section выбора
                        show_section('#sh1');
                        show_section('#sh2');
                        show_section('#sh3');
// действие при нажатии на section "Кассир"
                        $("#sh1").click(function () {
                            if ($(this).find('span').text() != 'Кассир') {
                                bt_sh1 = 1;
                                buttonOnAndOff();
                            } else {
                                bt_sh1 = 0;
                                buttonOnAndOff();
                            }
                        });
// действие при нажатии на section "Контрагент"
                        $("#sh2").click(function () {
                            if ($(this).find('span').text() != 'Контрагент') {
                                bt_sh2 = 1;
                                buttonOnAndOff();
                            } else {
                                bt_sh2 = 0;
                                buttonOnAndOff();
                            }
                        });
// действие при нажатии на section "Тип Сертификата"
                        $("#sh3").click(function () {
                            if ($(this).find('span').text() == 'Сертификат' && $("#sh4").text().length == 0) {
                                $(this).parent().parent().after('<section class="main">	<div class="wrapper-demo"><div id="sh4" class="wrapper-dropdown-3" tabindex="1"><span>Сертификат</span><ul class="dropdown">' + selection_kathir('certificates') + '	</ul></div></section>');
                                show_section('#sh4');
                                $("#sh4").click(function () {
                                    if ($(this).find('span').text() != 'Сертификат' && ((parseInt($(this).find('span').text().replace(/\s+/g, '')) != 0 && parseInt($(this).find('span').text().replace(/\s+/g, '')) >= parseFloat(sum)) || (parseInt($(this).find('span').text().replace(/\s+/g, '')) == 0))) {
                                        bt_sh3 = 1;
                                        buttonOnAndOff();
                                    } else {
                                        bt_sh3 = 0;
                                        buttonOnAndOff();
                                    }
                                });
                                bt_sh3 = 0;
                                buttonOnAndOff();
                            } else {
                                if ($("#sh3").find('span').text() != 'Тип Расчета') {
                                    bt_sh3 = 1;
                                    buttonOnAndOff();
                                } else {
                                    bt_sh3 = 0;
                                    buttonOnAndOff();
                                }
                                $("#sh4").remove();
                            }
                        });
// действие при нажатии на checkbox-1, "Ввод скидки"
                        $("#checkbox-1").click(function () {
                            if ($(this).attr('checked') == 'checked') {
                                $(this).next().after('<input type="text" id="sale" onKeyPress ="if (((event.keyCode < 48) || (event.keyCode > 57))&&(event.keyCode != 46)) event.returnValue = false;" maxlength="8" size="4" style="position:relative;left:20px;" placeholder="В рублях" /><label id="sum_sal" style="position:relative;left:30px;">Итого: <b style="color:red;">' + sum + '</b></label>');
                                chb_1 = 0;
                                buttonOnAndOff();
                                $("#sale").focusout(function () {
                                    sal = $(this).val();
                                    if (!show_discount(sal)) {
                                        chb_1 = 0;
                                        buttonOnAndOff();
                                    } else {
                                        bt_sale = $("input#sale").val();
                                        $("label#sum_sal").html('Итого: <b style="color:red;">' + (parseFloat(sum) - parseFloat(bt_sale)).toFixed(0) + '</b>');
                                        chb_1 = 1;
                                        buttonOnAndOff();
                                    }
                                });
                            } else {
                                $('#sale').remove();
                                $('#sum_sal').remove();
                                chb_1 = 1;
                                buttonOnAndOff();
                            }
                        });
// действие при нажатии на checkbox-2, "Ввод коменнтария"
                        $("#checkbox-2").click(function () {
                            if ($(this).attr('checked') == 'checked') {
                                $(this).next().after('<textarea id="coment" style="position:relative;left:20px;width: 50%;height: 18px;resize: none;" maxlength="200" placeholder="Введите комментарии по продаже..."></textarea>');
                                $('#coment').autoResize();
                                chb_2 = 0;
                                buttonOnAndOff();
                                $("#coment").focusout(function () {
                                    com = $(this).val();
                                    if (com.length >= 1 && com.length <= 200) {
                                        chb_2 = 1;
                                        buttonOnAndOff();
                                    } else {
                                        chb_2 = 0;
                                        buttonOnAndOff();
                                    }
                                });
                            } else {
                                $('#coment').remove();
                                chb_2 = 1;
                                buttonOnAndOff();
                            }
                        });
// действие при потере фокуса input-"Календаря"
                        $("#calendar").blur(function () {
                            if ($(this).val() == '') {
                                kal = 0;
                                buttonOnAndOff();
                            } else {
                                kalendar = $(this).val();
                                kal = 1;
                                buttonOnAndOff();
                            }
                        });
// функция валидации выбраных и введенных данных
                    function buttonOnAndOff() {
                        if ((bt_sh1 == 1 && bt_sh2 == 1 && bt_sh3 == 1 && chb_1 == 1 && chb_2 == 1) && ((kalendar != 0 && kal == 1) || (kalendar == 0))) {
                            $('.ui-button').show();
                            $('span.ui-button-text').text('Провести');
                            $('span.ui-button-text').attr('id', 'conduct_date');
                            bt_cashier = $("#sh1 span").attr('id');
                            bt_contractor = $("#sh2 span").attr('id');
                            bt_type_of_tax = $("#sh3 span").attr('id');
                            bt_certificate = $("#sh4 span").attr('id');
                            bt_sale = $("input#sale").val();
                            bt_coment = $("textarea#coment").val();
                        } else {
                            $('span.ui-button-text').text('Далее');
                            $('span.ui-button-text').attr('id', 'input_date');
                            $('.ui-button').hide();
                            bt_cashier = '';
                            bt_contractor = '';
                            bt_type_of_tax = '';
                            bt_certificate = '';
                            bt_sale = '';
                            bt_coment = '';
                        }
                    };

                        break;
                    case 'conduct_date' :

                        //функция проверки на пустую переменную
                    function empty_check(prm) {
                        if (prm == '' || prm == undefined) {
                            return prm = 'empty';
                        }
                        return prm;
                    }

                        ASPSESSID = "$2a$10$1g$Jnx4.wdOuTNgkIExWSOCxwToeIjqYmHtuNxg35K1Y0ABLPyTmy";
                        $.ajax({
                            url: "check_input.php",
                            type: "POST",
                            data: "cech=" + ASPSESSID + "&bt_cashier=" + empty_check(bt_cashier) + "&bt_contractor=" + empty_check(bt_contractor) + "&bt_type_of_tax=" + empty_check(bt_type_of_tax) + "&bt_certificate=" + empty_check(bt_certificate) + "&bt_sale=" + empty_check(bt_sale) + "&bt_coment=" + empty_check(bt_coment) + "&date_calendar=" + kalendar,
                            async: false,
                            cache: false,
                            success: function (res) {
                                switch (res) {
                                    case 'error' :
                                        jAlert_add('<h3>В результате обработки файла возникла непредвиденная ошибка, диалоговые окно будет закрыто!</h3>');
                                        jQuery("#dialog-message").dialog("close");
                                        jQuery('body').css("overflow", "visible");
                                        break;
                                    case 'error_old_query' :
                                        alert('Возникла ошибка обработки корзины, выберите заного новые товары');
                                        break;
                                    default:
                                        if (creat_check_pdf(res)) {
                                            $('#dialog-message').html("<center><a href='#' onclick='location.reload(); show_check();'>Форма вывода чека на печать</a></center>");
                                        }
                                        break;
                                }
                            }
                        });


                        $('span.ui-button-text').text('Завершить');
                        $(this).attr('id', 'cancel_date');
                        mess = '';
                        break;
                    // закрыть диалоговое окно и разрешить скрол на странице
                    case 'cancel_date' :
                        location.reload();
                        break;

                }

            });


        }
        ;

        //			# очистка корзины пользователя
        function dell_shop(id) {
            var id, shop;
            ASPSESSID = "$2a$10$1g$RBZzOWeDGBRS0A6pPpOyQ54sO3wre9ukhl7ylZIk5hOn4mEBly";
            $.ajax({
                url: "check_input.php",
                type: "POST",
                data: "cech=" + ASPSESSID + "&id=" + id,
                async: false,
                cache: false,
                success: function (res) {
                    chec_shoping_cart();
                    shop = sample_shoping();
                    if (shop == 0) {
                        shop = '';
                    }
                    $('table.deist').remove();
                    $('#dialog-message').html(shop);
                }
            });
            if (id == 'del_all_commodity') {
                location.reload();
            }

        }
        //			# прибавление/уменьшение количества
        function addition_shop(id, action, max_bd) {
            var id, action, kol, val, max_bd;

            $.each(jQuery('.bar_code'), function (index, val) {
                if ($(this).attr('id') == id) {
                    switch (action) {
                        case 'remove' :
                            kol = parseInt($(this).parent().find('input.cart_qty').val()) - 1;
                            val = parseInt($(this).parent().find('input.cart_qty').val());
                            break;
                        case 'adding' :
                            kol = parseInt($(this).parent().find('input.cart_qty').val()) + 1;
                            val = parseInt($(this).parent().find('input.cart_qty').val());
                            break;
                        case 'blur_input' :
                            kol = parseInt($(this).parent().find('input.cart_qty').val());
                            val = parseInt($(this).parent().find('input.cart_qty').attr('id'));
                            break;
                    }
                    //	alert(kol);
                    if (kol < 1) {
                        kol = 1;
                        $(this).parent().find('input.cart_qty').val(kol);
                    }
                    if (kol == 100) {
                        kol = 99;
                        $(this).parent().find('input.cart_qty').val(kol);
                    }

                    if (val != kol) {
                        //проверка чтоб kol не привышал max
                        if (kol >= max_bd) {
                            kol = max_bd;
                        }
                        $(this).parent().find('input.cart_qty').attr('id', kol);
                        ASPSESSID = "$2a$10$1g$HlwbEz0KJ/ILEiu3MnuhTgaCK6SndAq9sgFm.SfvGkr/n2hUle";
                        $.ajax({
                            url: "check_input.php",
                            type: "POST",
                            data: "cech_update=" + ASPSESSID + "&id_shop=" + id + "&new_kol=" + kol,
                            async: false,
                            cache: false,
                            success: function (res) {

                                switch (res) {
                                    case 'ok':
                                        break;
                                    case 'error':
                                        break;
                                }
                            }
                        });
                        $('table.deist').remove();
                        $('#dialog-message').html(sample_shoping());
                        chec_shoping_cart();
                    }
                }
            });


        }

        //			# заливка фона при выводе сообщения
        function alert_show() {
            jQuery('body').css("overflow", "hidden");					//убрать scrool на странице
            jQuery('body').append('<div class="fon_alert"></div>');  	//создание div для заливки фона
            jQuery('.fon_alert').fadeIn(300); 						 	//появление созданного div
            jQuery.alerts.okButton = '&nbsp;ОК&nbsp;';					//название кнопки ОК
        }
        //			# вывод сообщения
        function jAlert_add(mess) {
            alert_show();
            jAlert(mess, 'ВНИМАНИЕ', function (r) {
                jQuery('body').css("overflow", "visible");
                jQuery('.fon_alert').remove();
                return true;
            });
        }

        //			# Очистка сессий пользователей
        function delwin() {
            var del_ses = 1;
            jQuery.ajax({
                url: "check_input.php",
                type: "POST",
                data: "del_sessin=" + del_ses,
                async: false,
                cache: false,
                success: function (response) {
                }
            });
            setTimeout(function () {
                location.reload();
            }, 100); //перезагрузка страницы по окончанию времени
        }
        //			# Функция подсчета страниц
        function kol_vo_str(amount, name, sort) {
            var m;
            $.ajax({
                url: "check_input.php",
                type: "POST",
                data: "str_am=" + amount + "&sort=" + sort + "&bd_name=" + name,
                async: false,
                cache: false,
                success: function (res) {
                    switch (true) {
                        case ((res == 'all_one_page') && (isNaN(res) == true))    :
                            m = 1;
                            break;
                        case (isNaN(res) == false) :
                            m = res;
                            break;
                        default:
                            m = 1;
                            break;
                    }
                }
            });
            return m;
        }

        //			# Функция вывода данных базы данных
        function selection_bd(amount, page, sort, name, tb_name) {
            var m;
            $.ajax({
                url: "check_input.php",
                type: "POST",
                data: "str_amount=" + amount + "&sorting=" + sort + "&page_start=" + page + "&bd_name=" + name + "&tb_name=" + tb_name,
                async: false,
                cache: false,
                success: function (response) {
                    //	alert(response);
                    switch (true) {
                        case $.trim(response) == '<p>В базе данных не обнаружено таблицы проверте настройки</p>' :
                            m = 'error_find_bd';
                            break;
                        case $.trim(response) == '<p>Не обнаружено товаров в базе</p>' :
                            m = 'error_find_table';
                            break;
                        case $.trim(response).substr(0, 17) != "<tr class='even'>":
                            m = response;
                            break;
                        default:
                            m = $.trim(response);
                            break;
                    }
                }
            });

            return m;
        }

        //			# Функция вывода роли пользователя в системе
        function check_role_in_system() {
            var m;
            $.ajax({
                url: "check_input.php",
                type: "POST",
                data: "check_role_in_system=role_system&cech=$2a$10$1g$OiVxDEpPfpZqo/Qkfi.VPpI8HOI5i73tjMgk2YXp.v1Yen/GS2",
                async: false,
                cache: false,
                success: function (response) {
                    m = $.trim(response);
                }
            });
            return m;
        }

        //			# Функция добавление div Элементов для построничной навигации
        function add_div_element_jPaginator(div) {
            $(div).after('<div id="container"> <div id="pagination"> <a class="control" id="max_backward"></a> <a class="control" id="over_backward"></a> <div class="paginator_p_wrap"> <div class="paginator_p_bloc"> </div> </div> <a class="control" id="over_forward"></a><a class="control" id="max_forward"></a><div class="paginator_slider" class="ui-slider ui-slider-horizontal ui-widget ui-widget-content ui-corner-all">  <a class="ui-slider-handle ui-state-default ui-corner-all" href="#"></a> </div> </div> </div>');
        }

        //			# Функция добавление div Элементов для построничной навигации
        function add_dialog_message(text, heig, wid, closeOn, titles) {
            alert_show();
            //jQuery('body').css("overflow","hidden");
            jQuery('body').append('<div id="dialog-message" style="display:none" title=""><p class="reasdss"></p></div>');
            //$("#dialog-message").hover(function(){$("body").css("overflow","hidden");});
            jQuery("#dialog-message").html(text).dialog({
                modal: true,
                closeOnEscape: closeOn,
                title: titles,
                show: "slide",
                resizable: true,
                height: heig,
                width: wid,
                beforeClose: function (event, ui) {
                    jQuery('body').css("overflow", "visible");
                    jQuery('.fon_alert').remove();
                },
                buttons: {
                    Далее: function () {

                    }
                }
            });
            //скрыть плагина заливку фона
            jQuery('.ui-widget-overlay').remove();
        }

        //			# Добавления товара в корзину
        function add_shop(mgz, price) {
            var res, mgz;
            ASPSESSID = "$2a$10$1g$YEL5gDE5FpsDbltGZfuwXmHZTl4I.UylIwvUrWIqSCHU0.wC3G";
            $.ajax({
                url: "check_input.php",
                type: "POST",
                data: "id_commodity=" + mgz + "&asp=" + ASPSESSID + "&prise_sale=" + price,
                async: false,
                cache: false,
                success: function (response) {
                    switch (response) {
                        case 'error':
                            alert('Возникла ошибка добавления');
                            break;

                        default:

                            $.each(jQuery('a.add_shop'), function (index, val) {
                                id = $(val).attr("id");
                                if (parseInt(id) == parseInt(mgz)) {
                                    $(this).children().removeClass().addClass('active_shoping_cart');
                                    $(this).parent().html('<a href="#" class="show_shop" onclick="active_shoping_cart();"><span class="active_shoping_cart"></span></a>');
                                }
                            });
                            chec_shoping_cart();
                            break;

                    }
                }
            });
        }

        //			# Выбрать Пользователей или Контрагента с Базы
        function selection_kathir(name_bd) {
            ASPSESSID = "$2a$10$1g$MluiZ0BrByI3saWyE.exHpON79CAzh.HVgxutSRWc4GYfIsr6S";
            var name = 0, str;
            $.ajax({
                url: "check_input.php",
                dataType: "json",
                type: "POST",
                data: "sid=" + ASPSESSID + "&name_bd=" + name_bd,
                async: false,
                cache: false,
                success: function (res) {

                    switch (res.date.names) {
                        case 'error' :
                            return false;
                            break;
                        case 'undefined' :
                            return false;
                            break;
                        default:
                            name = res.date.names;
                            break;
                    }
                    str = '';
                    for (var key in name) {
                        //str +='<option value="'+key+'">'+name[key]+'</option>';
                        str += '<li><a href="#" id="' + key + '">' + name[key] + '</a></li>';
                    }
                }
            });

            return str;

        }

        function show_discount(price) {
            price = parseFloat(price);
            if (typeof(price) != 'number') return false;
            var m;
            ASPSESSID = "$2a$10$1g$NVjTFTXw8hJ48puuBLu6diGdob9giDXeA17KYHSWn/JgnAGTy2";
            $.ajax({
                url: "check_input.php",
                type: "POST",
                data: "cech=" + ASPSESSID + "&discount=" + price,
                async: false,
                cache: false,
                success: function (res) {
                    m = res;
                }
            });
            if (m == 'ok') {
                return true;
            } else {
                return false;
            }
        }
        ;

        //			# Начало проверки на выявление ошибок
        function error_find_bd(str, colspan) {
            switch (str) {
                case 'error_find_table' :
                    str = '<tr><td colspan="' + colspan + '" style="text-align:center;font-size:15px;font-weight: 600;border: #838383  1px solid; background: -webkit-gradient(linear, left top, left bottom, from(#FAFAFA), to(rgba(118, 244, 0, 0.66)));">Не найдено ни одной записи</td></tr>';
                    break;
                case 'error_find_bd' :
                    str = '<tr><td colspan="' + colspan + '" style="text-align:center;font-size:15px;font-weight: 600;border: #838383  1px solid;background: -webkit-gradient(linear, left top, left bottom, from(#FAFAFA), to(rgba(69, 108, 168, 0.66)));">Возникла ошибка подключения к базе</td></tr>';
                    break;
                case 'error' :
                    str = '<tr><td colspan="' + colspan + '" style="text-align:center;font-size:15px;font-weight: 600;border: #838383  1px solid;background: -webkit-gradient(linear, left top, left bottom, from(#FAFAFA), to(rgba(69, 108, 168, 0.66)));">Возникла непредвиденная ошибка</td></tr>';
                    break;
                //	default: data_mess=str; break;
            }
            return str;
        }
        ;
        //			# Начало выполнения функции для постраницчной навигации
        function show_str(bd_name, sql, amount, page, tb_name, no_data) {
//			# начало проверки, есть ли ошибки в выводе "page", либо всего одна страница						
            $('#container').show();
            $("#pagination").jPaginator({

                nbPages: page,			// количество страниц
                marginPx: 2,					// растояние между кнопками
                length: 8, 					// количество выводимых кнопок
                overBtnLeft: '#over_backward',
                overBtnRight: '#over_forward',
                maxBtnLeft: '#max_backward',
                maxBtnRight: '#max_forward',
                onPageClicked: function (a, num) {

                    data_mess = error_find_bd(selection_bd(amount, num, sql, bd_name, tb_name), no_data);

                    if (page == 1) {
                        $('#container').hide();
                    } else {
                        $('#container').show();
                    }
                    $('tbody#top').html(data_mess)
                }
            });
        }
        ;
        //									# конец <<<

        // 				# Функция для сохранения отредактированного текста с помощью ajax
        function savedata(tx_id, tx_nm, tx_am, tx_cf) {
            ASPSESSID = '$2a$10$1g$dy63iVLZzxV.6HI/dMeRWGPxX4iILrXVR2ilo2F/Fuy051oyJa';
            $.ajax({
                url: 'check_input.php',             // url который обрабатывает и сохраняет наш текст
                type: 'POST',
                async: true,
                cache: false,
                data: {
                    cech: ASPSESSID,		// кеш
                    d_nm: tx_nm,     		// Название товара
                    d_am: tx_am,    		// Кол-во товаров
                    d_cf: tx_cf,     		// Коэффициент
                    id_elt: tx_id			// id элемента
                },
                success: function (data) {      //получили ответ от сервера - обрабатываем
                    //	m			= data.split('=');
                    if (data != 'error')   //сервер прислал нам отредактированый текст, значит всё ок
                    {
                        //    $('#'+elementidsave).html(data);        //записываем присланные данные от сервера в элемент, который редактировался
                        $('<div id="status" style="text-align:center;font-size:15px;font-weight: 600;border: #838383  1px solid; background: -webkit-gradient(linear, left top, left bottom, from(#FAFAFA), to(rgba(118, 244, 0, 0.66)));" >Данные успешно сохранены!</div>')     //выводим сообщение об успешном ответе сервера
                            .insertAfter('body')
                            .addClass("success")
                            .fadeIn('fast')
                            .delay(1000)
                            .fadeOut('slow', function () {
                                this.remove();
                            }); //уничтожаем элемент

                    }
                    else {
                        $('<div id="status" style="text-align:center;font-size:15px;font-weight: 600;border: #838383  1px solid; background: -webkit-gradient(linear, left top, left bottom, from(#FAFAFA), to(rgba(234, 34, 186, 0.66)));">Запрос завершился ошибкой:' + data + '</div>') // выводим данные про ошибку
                            .insertAfter('body')
                            .addClass("error")
                            .fadeIn('fast')
                            .delay(3000)
                            .fadeOut('slow', function () {
                                this.remove();
                            });  //уничтожаем элемент
                    }
                }
            });
        }

        function getChar(event) {
            if (event.which == null) {
                if (event.keyCode < 32 || event.keyCode == 46) return null;
                return String.fromCharCode(event.keyCode) // IE
            }

            if (event.which != 0 && event.charCode != 0) {
                if (event.which < 32 || event.keyCode == 46) return null;
                return String.fromCharCode(event.which)   // остальные
            }

            return null; // специальная клавиша
        }

        var bar_code = '', name_commodity = '', amount = '', coefficient = '', price_sale = '';
        // 				# Функция для сохранения отредактированного текста с помощью ajax
        function editing_commodity(e) {

            if (bar_code != '') {
                jAlert_add('<h3>Отмените последнее дейстивие редактирования!</h3>');
                jQuery("#dialog-message").dialog("close");
                jQuery('body').css("overflow", "visible");
                return;
            }

            bar_code = parseInt(e);
            d_nm = $('#' + bar_code).closest('tr').find('.name_commodity');
            d_am = $('#' + bar_code).closest('tr').find('.amount');
            d_cf = $('#' + bar_code).closest('tr').find('.coefficient');
            d_pr = $('#' + bar_code).closest('tr').find('.price_sale');


            name_commodity = d_nm.text();
            amount = parseInt(d_am.text());
            coefficient = parseFloat(d_cf.text());
            price_sale = parseFloat(d_pr.attr('id'));
            editing_comm = $('#' + bar_code).closest('tr').find('.editing_comm');
            active_comm = $('#' + bar_code).closest('tr').find('span.active_editing_commodity');

            d_nm.html('<input class="c_nm" size="' + name_commodity.length + '" 	value="' + name_commodity + '" type="text" maxlength="200" style="width: 100%;"/>');
            d_am.html('<input class="c_am" size="' + d_am.text().length + '" 	value="' + amount + '" type="text" style="width: 100%;text-align:center;" maxlength="4" onKeyPress ="if (((event.keyCode < 48) || (event.keyCode > 57))) event.returnValue = false;"/>');
            d_cf.html('<input class="c_cf" size="' + d_cf.text().length + '" 	value="' + coefficient + '" type="text" style="width: 35%;text-align:center;" maxlength="4" onKeyPress ="if (((event.keyCode < 48) || (event.keyCode > 57))&&(event.keyCode != 46)) event.returnValue = false;" onblur="calc_price(' + bar_code + ');"/>');

            editing_comm.attr('onclick', 'save_commodity(' + bar_code + ');');
            active_comm.removeClass('active_editing_commodity').addClass('editing_commodity');
            $('html, body').animate({scrollTop: $('#' + bar_code).position().top}, 'slow');
            //alert('*'+bar_code+'*'+name_commodity+'*'+amount+'*'+coefficient+'*'+price_sale);

            /*	m = e.split('#');
             clas 		= m[0];
             bar_code 	= m[1];
             val = $('#'+bar_code).closest('tr').find('.'+clas);
             var val_t = val.text();
             var input = $('#editbox').val();
             val.removeAttr('onclick');
             if( val_t!=input  || input!=undefined  || input!='' ){

             val.find('#editbox').focus();
             val.html('<input id="editbox" size="'+ val.text().length+'" value="' + val.text() + '" type="text" style="width: 100%;"/>');

             }
             /*$('.coefficient').blur(function (event)
             {
             var m = $(this).val();
             alert(m);
             $(this).closest('td').text(m);
             });*/
            /*	$('input#editbox').blur(function (event)
             {

             var m = $(this).val();
             //alert(m);
             if(m != input && m != ''){
             $(this).closest('td').text(m); }
             if(m == '' || m != undefined){$(this).closest('td').text(input); }
             }

             );*/
        }
        // редактирование стоимость
        function calc_price(e) {
            // Запрет повторного выбора для редактирования
            if (bar_code == '') {
                jAlert_add('<h3>Ошибка изменения данных!</h3>');
                jQuery("#dialog-message").dialog("close");
                jQuery('body').css("overflow", "visible");
                return;
            }
            price_purchase = parseInt($('#' + bar_code).closest('tr').find('.price_purchase').attr('id'));
            price_shipping = parseInt($('#' + bar_code).closest('tr').find('.price_shipping').attr('id'));
            c_cf = parseFloat($('#' + bar_code).closest('tr').find('.c_cf').val());
            // Проверка на пустые переменные
            if (c_cf == '' || c_cf == '0' || c_cf == 0 || c_cf == undefined || isNaN(c_cf) == true) {
                $('#' + bar_code).closest('tr').find('.c_cf').val(coefficient);
                jAlert_add('<h3>Введите корректные данные!</h3>');
                jQuery("#dialog-message").dialog("close");
                jQuery('body').css("overflow", "visible");
                return;
            }
            itg = Math.ceil((price_purchase + price_shipping) * c_cf);
            d_itg = '<b>' + numFormat(itg, '.', ' ') + ' руб.</b>';
            $('#' + bar_code).closest('tr').find('.price_sale').html(d_itg);
        }
        // сохранение в базу
        function save_commodity(e) {

            c_cd = parseInt(e);
            c_nam = $('#' + c_cd).closest('tr').find('input.c_nm').val();
            c_amt = $('#' + c_cd).closest('tr').find('input.c_am').val();
            c_cof = $('#' + c_cd).closest('tr').find('input.c_cf').val();
            c_pr = $('#' + c_cd).closest('tr').find('input.c_pr').attr('id');
            c_edt = $('#' + c_cd).closest('tr').find('.editing_comm');
            c_act = $('#' + c_cd).closest('tr').find('span.editing_commodity');
            // проверка на на совпадающие артикул
            if (c_cd != bar_code) {
                jAlert_add('<h3>Ошибка Выбраны неверный артикул!</h3>');
                jQuery("#dialog-message").dialog("close");
                jQuery('body').css("overflow", "visible");
                return;
            }
            // проверка на пустые пременные
            if ((name_commodity == '' || amount == '' || coefficient == '') && (c_nam == '' || c_amt == '' || c_nam == undefined || c_amt == undefined)) {
                $('#' + c_cd).closest('tr').find('input.c_nm').val(name_commodity);
                $('#' + c_cd).closest('tr').find('input.c_am').val(amount);
                jAlert_add(amount + '<h3>Ошибка! Данных не обнаружено!</h3>');
                jQuery("#dialog-message").dialog("close");
                jQuery('body').css("overflow", "visible");
                return;
            }
            // провека сохранять или нет данные
            if (name_commodity != c_nam || amount != c_amt || coefficient != c_cof) {
                //alert('сохранить новые данные'+$.trim(c_nam)+c_amt+c_cof);
                savedata(bar_code, $.trim(c_nam), c_amt, c_cof);   // отправляем на сервер
            } else {
                //	jAlert_add('<h3>Ошибка! Нет новых данных!</h3>'); jQuery("#dialog-message").dialog("close");jQuery('body').css("overflow","visible");
            }
            c_nam = $('#' + c_cd).closest('tr').find('.name_commodity').text(c_nam);
            c_amt = $('#' + c_cd).closest('tr').find('.amount').text(c_amt);
            c_cof = $('#' + c_cd).closest('tr').find('.coefficient').text(c_cof);

            c_edt.attr('onclick', 'editing_commodity(' + c_cd + ');');
            c_act.removeClass('editing_commodity').addClass('active_editing_commodity');
            $('html, body').animate({scrollTop: $('#' + bar_code).position().top}, 'slow');
            bar_code = '';
        }
        function dateIsCorrect(dateString) {
            var parts = dateString.split('-');
            if (parts.length != 3) return false;
            try {
                var tmpDate = new Date(parts[0], parts[1], parts[2], 12);
                dateString == tmpDate.getFullYear() + '-' + tmpDate.getMonth() + '-' + tmpDate.getDate();
                return dateString;
            } catch (ex) {
                return false;
            }
        }
        $(document).ready(function () {

            $('input#date_from').will_pickdate({
                format: 'Y-m-d',
                inputOutputFormat: 'Y-m-d',
                days: ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'],
                months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
                timePicker: false,
                militaryTime: false,
                allowEmpty: true,
                yearsPerPage: 3,
                allowEmpty: true
            });

            $('input#to_date').will_pickdate({
                format: 'Y-m-d',
                inputOutputFormat: 'Y-m-d',
                days: ['Пн', 'Вт', 'Ср', 'Чт', 'Пт', 'Сб', 'Вс'],
                months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
                timePicker: false,
                militaryTime: false,
                allowEmpty: true,
                yearsPerPage: 3,
                allowEmpty: true
            });

// проверка существует ли переменная
            if (window.after_div !== undefined && top_name_table !== undefined) {
                var str_amount = $("select#selec_element").val(); // количество товаров на одной странице
                var chek_tb = $('#tovar').html(top_name_table + '<tbody id="top"></tbody>');
                add_div_element_jPaginator(after_div);					// добавление div Элементов для построничной навигации
                kol_page = kol_vo_str(str_amount, bd_name, table_name);	// количество страниц
                show_str(bd_name, sorting, str_amount, kol_page, table_name, no_data);
            }

            $('#revenue_next').bind('click', function () {

                var
                    from = $('#date_from_display').val(),
                    date = $('#to_date').val(),
                    slt_data = $('select#select_providers :selected').attr("id"),
                    s_srt = ''
                r_srt = '',
                    error = '';
//	проверка на пустоту
                if ((from == '' || from == undefined || from == null) || (date == '' || date == undefined || date == null)) {
                    alert(from + 'пустые данные');
                    return;
                }
//	проверка на правильный ввод даты
                if (dateIsCorrect(from) == false && dateIsCorrect(date) == false) {
                    alert(from + 'не подходи к дате');
                    return;
                } else {

                    if (slt_data == 0) {
                        slt_data = 'default';
                    }
                    ASPSESSID = '$2a$10$1g$GPADN17zmx8nlalTfrOOocSG2a/gvm7HWGyNFd1P5eG.0zUtbG';
                    $.ajax({
                        url: 'check_input.php',             // url который обрабатывает и сохраняет наш текст
                        dataType: "json",
                        type: 'POST',
                        data: {
                            cech: ASPSESSID,		// кеш
                            d_from: from,     		// дата от
                            d_date: date,  			// дата до
                            data_selected: slt_data,   	// данные выбранного постащика
                        },
                        async: false,
                        cache: false,
                        success: function (det) {

                            if (det.error == undefined) {
                                stc = det.static;		// массив со статистикой продаж
                                rvn = det.revenue;		// массив с отчетом выручки

                                s_id_seling = stc.id_seling;
                                s_bar_code = stc.bar_code;
                                s_artical = stc.artical;
                                s_name_com = stc.name_com;
                                s_name_prv = stc.name_prv;
                                s_amount = stc.amount_static;
                                s_date = stc.selling_date;
                                s_seller = stc.seller;

                                r_id_check = rvn.id_check;
                                p_price = rvn.purchase_price;
                                d_price = rvn.delivery_price;
                                rl_price = rvn.realz_price;
                                rv_price = rvn.revenue_price;
                                type_cash = rvn.type_cash;


                                for (var key in s_id_seling) {
                                    s_srt += "<tr class='even' onkeydown='if(event.keyCode==9) return false;'> <td class='bar_code' id='" + parseInt(s_bar_code[key]) + "'>" + s_artical[key] + "</td> <td class='name_commodity' style='text-align:left;' >" + s_name_com[key] + "</td> <td class='name_provider' style='text-align:left;' >" + s_name_prv[key] + "</td> <td class='amount_selling' >" + parseInt(s_amount[key]) + "</td> <td class='date_selling' >" + s_date[key] + "</td> <td class='user_selling' >" + s_seller[key] + "</td> </tr>";
                                }

                                for (var key in r_id_check) {
                                    if (r_id_check[key] != 'summa_price') {
                                        var style_cash = '', cash = '';
// Тип Операции						
                                        switch (type_cash[key]) {
                                            case 0 :
                                                cash = 'Наличный';
                                                style_cash = "style='background: -webkit-gradient(linear, left top, left bottom, from(#F7F9F8), to(#1DF47A));'";
                                                break;
                                            case 1 :
                                                cash = 'Безналичный';
                                                style_cash = "style='background: -webkit-gradient(linear, left top, left bottom, from(#F7F9F8), to(#39D4E0));'";
                                                break;
                                            case 2 :
                                                cash = 'Сертификат';
                                                style_cash = "style='background: -webkit-gradient(linear, left top, left bottom, from(#F7F9F8), to(#EBF021));'";
                                                break;
                                            case 3 :
                                                cash = 'Бесплатно';
                                                style_cash = "style='background: -webkit-gradient(linear, left top, left bottom, from(#F7F9F8), to(#F79EEB));'";
                                                break;
                                            case 4 :
                                                cash = 'Брак';
                                                style_cash = "style='background: -webkit-gradient(linear, left top, left bottom, from(#F5FFFB), to(#F96F0D));'";
                                                break;
                                        }

                                        r_srt += "<tr class='even' onkeydown='if(event.keyCode==9) return false;'> <td class='id_check' id='" + parseInt(r_id_check[key]) + "'>" + parseInt(r_id_check[key]) + "</td> <td class='purchase_price' >" + numFormat(Math.round(parseFloat(p_price[key])), '.', ' ') + "</td> <td class='delivery_price' >" + numFormat(Math.round(parseFloat(d_price[key])), '.', ' ') + "</td> <td class='realz_price' >" + numFormat(Math.round(parseFloat(rl_price[key])), '.', ' ') + "</td> <td class='revenue_price' >" + numFormat(Math.round(parseFloat(rv_price[key])), '.', ' ') + "</td> <td class='type_cash' " + style_cash + " >" + cash + "</td> </tr>";
                                    }
                                }
                                if (r_id_check[key] == 'summa_price') {
                                    r_srt += "<tr class='even' onkeydown='if(event.keyCode==9) return false;'><td class='id_check' id='" + r_id_check[key] + "' style='font-size:15px; font-weight: 800; background: -webkit-gradient(linear, left top, left bottom, from(#FCFCFC), to(#46DD42));' ><b>Итого:</b></td><td class='purchase_price' style='font-size:15px; font-weight: 800; background: -webkit-gradient(linear, left top, left bottom, from(#FCFCFC), to(#46DD42));'><b>" + numFormat(Math.round(parseFloat(p_price[key])), '.', ' ') + "</b></td> <td class='delivery_price' style='font-size:15px; font-weight: 800; background: -webkit-gradient(linear, left top, left bottom, from(#FCFCFC), to(#46DD42));'><b>" + numFormat(Math.round(parseFloat(d_price[key])), '.', ' ') + "</b></td> <td class='realz_price' style='font-size:15px; font-weight: 800; background: -webkit-gradient(linear, left top, left bottom, from(#FCFCFC), to(#46DD42));'><b>" + numFormat(Math.round(parseFloat(rl_price[key])), '.', ' ') + "</b></td> <td class='revenue_price' style='font-size:15px; font-weight: 800; background: -webkit-gradient(linear, left top, left bottom, from(#FCFCFC), to(#46DD42));'><b>" + numFormat(Math.round(parseFloat(rv_price[key])), '.', ' ') + "</b></td><td class='revenue_price' style='font-size:15px; font-weight: 800; background: -webkit-gradient(linear, left top, left bottom, from(#FCFCFC), to(#46DD42));'><b></b></td> </tr>";
                                }
                            } else {

                                r_srt = '<tr id="error_revenue"><td colspan="6" style="text-align:center;font-size:15px;font-weight: 600;border: #838383  1px solid; background: -webkit-gradient(linear, left top, left bottom, from(#FAFAFA), to(rgba(118, 244, 0, 0.66)));">Укажите верный параметр сортировки</td></tr>';
                                s_srt = '<tr id="error_statics"><td colspan="6" style="text-align:center;font-size:15px;font-weight: 600;border: #838383  1px solid; background: -webkit-gradient(linear, left top, left bottom, from(#FAFAFA), to(rgba(118, 244, 0, 0.66)));">Укажите верный параметр сортировки</td></tr>';

                            }
                        }

                    });

                    //alert(error);
                    $('#top_revenue').html(r_srt);
                    $('#top_statics').html(s_srt);
                }

            });

            $('#add_bd_commodity').bind('dblclick ', function () {

                // показать кнопку закрытие
                $('.ui-dialog-titlebar-close').show();
                // сообщение с загрузкой файла
                mess = "<b>Выбирите нужный вид добавления товаров в систему:</b> <table class='deist_for_xls' border='0' cellspacing='0'><tr style='padding: 10px;'> <td>Загрузка с Excel </td> <td> <input class='check_commodity' type='checkbox' /> </td> </tr><tr><td> Работа со штрих кодером</td><td><input type='checkbox' class='check_commodity'/></td></tr> <td>Добавление единичного товара</td><td><input type='checkbox' class='check_commodity'/></td></tr></table>";
                add_dialog_message(mess, 200, 400, false, '');
                var checking = $("input:checkbox:checked.check_commodity");
                $('.ui-button').bind('click', function () {

                    if (checking.length == 1) {
                        type_add_commodity = $.trim(checking.closest('tr').text());

                        switch (type_add_commodity) {

                            case 'Загрузка с Excel':

                                switch (jQuery('span.ui-button-text').attr('id')) {

                                    case 'excel_1' :

                                        var files_names = jQuery('#files_names').attr('href');
                                        var cache = 'cache';

                                        $.ajax({
                                            url: "check_excel.php",
                                            type: "POST",
                                            data: "files_names=" + files_names + "&cache=" + cache,
                                            async: true,
                                            cache: false,
                                            success: function (chec_excel) {
                                                //	alert(chec_excel);
                                                switch (true) {
                                                    case chec_excel == 'Not suitable on template' :
                                                        jAlert_add('<h3>Файл не подходит по шаблону, загрузите другой файл!</h3>');
                                                        jQuery("#dialog-message").dialog("close");
                                                        jQuery('body').css("overflow", "visible");
                                                        break;
                                                    case chec_excel == 'Not is the installation version' :
                                                        jAlert_add('<h3>Версия файла неявляется рекомендуемой для загрузки, загрузите новую версию файла!</h3>');
                                                        jQuery("#dialog-message").dialog("close");
                                                        jQuery('body').css("overflow", "visible");
                                                        break;
                                                    case chec_excel == 'error_del_file' :
                                                        jAlert_add('<h3>Возникла ошибка удаления файла, диалоговое окно будет закрыто!</h3>');
                                                        jQuery("#dialog-message").dialog("close");
                                                        jQuery('body').css("overflow", "visible");
                                                        break;
                                                    case chec_excel == 'no file' :
                                                        jAlert_add('<h3>Загруженный файл не найден в системе, диалоговые окно будет закрыто!</h3>');
                                                        jQuery("#dialog-message").dialog("close");
                                                        jQuery('body').css("overflow", "visible");
                                                        break;
                                                    case chec_excel == 'del_file' :
                                                        jQuery("#dialog-message").dialog("close");
                                                        jQuery('body').css("overflow", "visible");
                                                        break;
                                                    case chec_excel.slice(0, 49) == "<h3>Найдены следующие ошибки в спецификации:</h3>" :
                                                        $("#dialog-message").html(chec_excel);
                                                        break;
                                                    case chec_excel == "<b>Возникла непредвиденная ошибка<b>" :
                                                        jAlert_add('<h3>В результате обработки файла возникла непредвиденная ошибка, диалоговые окно будет закрыто!</h3>');
                                                        jQuery("#dialog-message").dialog("close");
                                                        jQuery('body').css("overflow", "visible");
                                                        break;
                                                    case chec_excel.slice(0, 5) == "<h3>Найдены следующие ошибки в спецификации:</h3>" :
                                                        $("#dialog-message").html(chec_excel);
                                                        break;
                                                    default:
                                                        $("#dialog-message").html(chec_excel);
                                                        $('span.ui-button-text').attr('id', 'excel_end').text('Завершить');
                                                        break;

                                                }
                                            }

                                        });

                                        break;
                                    case 'excel_end' :
                                        jQuery("#dialog-message").dialog("close");
                                        location.reload();
                                        break;

                                    default:

                                        mes = '<h3>Загрузка списка товаров согласно установленному шаблону:</h3><div id="Buttons">    <span id="UploadPhotos"> <input type="button" id="Progress" /><i id="fAddPhotos"></i><input type="button" id="AddPhotos" value="Загрузка" />  </span></div>';
                                        $("#dialog-message").html(mes);
                                        ASPSESSID = "asdasd123109oijflsdgk30";
                                        $('.ui-button').hide();
                                        //функция обработки загрузки
                                        BindSWFUpload();

                                        break;

                                }

                                break;

                            case 'Работа со штрих кодером':

                                jQuery('span.ui-button-text').attr('id', 'barcoder_1');
                                alert('Работа со штрих кодером');

                                break;

                            case 'Добавление единичного товара':

                                jQuery('span.ui-button-text').attr('id', 'add_single_1');
                                alert('Добавление единичного товара');

                                break;

                        }


                    } else {
                        jAlert("<h2> Выбирите тип добавления товаров! </h2>", 'ВНИМАНИЕ', function (r) {
                        });
                    }

                });

// разрешение выбирать только один checkbox
                $('.check_commodity').bind('click', function () {
                    checking = $("input:checkbox:checked.check_commodity");
                    if (checking.length > 1) {
                        return false;
                    }
                });

            });
// 		сортировка таблиц по колонкам
            $("tr th.sort_both").click(function () {

                var table_name = $(this).attr('id'),
                    sorting = $(this).attr('class');

                switch (sorting) {
                    case 'sort_both' :
                        $('.sort_asc').removeClass().addClass('sort_both');
                        $('.sort_desc').removeClass().addClass('sort_both');
                        $(this).removeClass().addClass('sort_asc');
                        sorting = 'sort_asc';
                        break;
                    case 'sort_asc'  :
                        $('.sort_desc').removeClass().addClass('sort_both');
                        $(this).removeClass().addClass('sort_desc');
                        sorting = 'sort_desc';
                        break;
                    case 'sort_desc' :
                        $('.sort_asc').removeClass().addClass('sort_both');
                        $(this).removeClass().addClass('sort_asc');
                        sorting = 'sort_asc';
                        break;
                }
                data_mess = error_find_bd(selection_bd(str_amount, str_num, sorting, bd_name, table_name), no_data);
                $('tbody#top').html(data_mess);
                $('div.selected').removeClass().addClass('paginator_p');

            });

//		выбор сколько строк таблицы выводить
            $("select#selec_element").click(function () {

                if ($(this).val() != str_amount) {
                    str_amount = $(this).val();
                    data_mess = error_find_bd(selection_bd(str_amount, str_num, sorting, bd_name, table_name), no_data);
                    kol_page = kol_vo_str(str_amount, bd_name, table_name);
                    show_str(bd_name, sorting, str_amount, kol_page, table_name, no_data);

                    $('tbody#top').html(data_mess);
                }

            });
// 		очистить значение input поиска при потере фокуса
            $("input#searc").blur(function () {
                if ($(this).val() == '') data_mess = error_find_bd(selection_bd(str_amount, str_num, sorting, bd_name, table_name), no_data);
                $('tbody#top').html(data_mess);
            });
// 		функция поиска 	
            $("input#searc").keyup(function () {
                input = $(this).val();
                if (input.length > 2) {
                    sorting = 'sort_like_' + bd_name;
                    data_mess = error_find_bd(selection_bd(str_amount, input, sorting, bd_name, table_name), no_data);


                    $('tbody#top').html(data_mess);
                    $('#container').hide();
                }
            });

        });

    </script>
    <div class="menu_container">


        <b>Пользователь,</b> <i><?php echo $inicial; ?></i><br><i><u><?php echo $dolz; ?></u></i><br><br>

        <?php

        //доработать блок
        $cilk = $_SERVER['REQUEST_URI'];            //ссылка на которой находиться пользователь
        $cilk = substr(strrchr($cilk, "/"), 1);  //удаление текста до "/"-символа
        $cilk = explode("?", $cilk);          //удаление текста после "?"-символа
        if ($cilk[0] == 'red_polz.php') {
            $cilka = $cilk[0];
            $cilka = 'zaiavki.php';
        }            //присвоение 'red_polz.php' путь 'zaiavki.php'
        if ($cilk[0] == 'red_inf_rashet.php') {
            $cilka = $cilk[0];
            $cilka = 'computation.php';
        }        //присвоение 'red_inf_rashet.php' путь 'computation.php'
        if ($cilk[0] != 'red_inf_rashet.php' && $cilk[0] != 'red_polz.php') {
            $cilka = $cilk[0];
        }    //проверка если условия не прошли
        if ($dolz == "Администратор" || $dolz == "Директор") {
            $rol = "";
        } else {
            $rol = "display:none";
        }
        //доработать блок
        ?>
        <ul class="nav" id="bloc">
            <li><a href="vhod.php" style="display:block"         <?= ($cilka == 'vhod.php') ? ' id="dddd"' : '' ?>>Главное</a>
            </li>
            <li><a href="bd_tovarov.php"
                   style="display:block"         <?= ($cilka == 'bd_tovarov.php') ? ' id="dddd"' : '' ?>>База
                    Товаров <?php echo $num; ?></a></li>
            <li><a href="bd_provider.php"
                   style="display:block"         <?= ($cilka == 'bd_provider.php') ? ' id="dddd"' : '' ?>>База
                    Поставщиков </a></li>
            <li><a href="histori_selling.php"
                   style="display:block"         <?= ($cilka == 'histori_selling.php') ? ' id="dddd"' : '' ?>>История
                    Продаж </a></li>
            <li><a href="report_selling.php"
                   style="<?php echo $rol; ?>"     <?= ($cilka == 'report_selling.php') ? ' id="dddd"' : '' ?>>Отчеты </a>
            </li>
            <li><a onclick="delwin();">Выход</a></li>
        </ul>
    </div>
    </html>
<?php else : header("Location: ./../index.php");endif; ?>

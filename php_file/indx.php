<?php session_start();


require_once("bd.php");
require_once('check_pdf/mc_table.php');

//������� ������ ��� ������ �� XSS � SQ-��������
function escape($string)
{
    global $db;
    if (!get_magic_quotes_gpc())
        return mysqli_real_escape_string($db, $string);
    else
        return mysqli_real_escape_string($db, stripslashes($string));
}

//������� ������� ������ �� ����
function amount_bd($id_check, $msg)
{
    global $db;
    switch ($msg) {
        case 'check' :
            $sql = "select
					check.id_check,
					id_company_inn,
					number_items,
                    id_certificates,
					discount,
					check.price_purchase,
					check.price,
					price_total,
                    cash,
					date_selling,
					user_selling,
                    number_commodity,
                    commodity.id_article,
                    selling.bar_code,
                    commodity.name_commodity,
                    amount_celling,
                    price_selling
			from `check`
            left join `selling` on (`check`.`id_check`=`selling`.`id_check`)
            left join `commodity` on (`selling`.`bar_code`=`commodity`.`bar_code`)
            where check.id_check = '%s'";
            $query = sprintf($sql, $id_check);
            break;
        case 'counterparties' :
            $sql = "select
					id_company_inn,
					full_name,
					legal_address,
                    actual_address,
					phone_number,
					email_company,
                    type_of_tax
			from `counterparties`
            where id_company_inn = '%s'";

            $query = sprintf($sql, $id_check);
            break;
        case 'users' :
            $sql = "select
					id_users,
					functions.names as fun,
                    users.names as nam,
					surnames,
					patronymic,
                    number_phone
			from `users`
			left join `functions` on (`functions`.`id_function`=`users`.`function`)
            where id_users = '%s'";

            $query = sprintf($sql, $id_check);
            break;

        case 'check_users' :
            $sql = "select
					id_users,
                    email,
					function
			from `users`
            where id_users = '%s' and login = '" . $_SESSION['login'] . "' and password = '" . $_SESSION['password'] . "'";

            $query = sprintf($sql, $id_check);
            break;


    }


    $result = mysqli_query($db, $query);
    if (mysqli_error($db)) {
        return mysqli_error($db);
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

//���� ��������
function write_price_in_words($price)
{
    $price = number_format($price, 2, '.', '');
    $point = strpos($price, '.');
    //�������� ����� �� ������
    if (!empty($point)) {
        $rub = mb_substr($price, 0, $point);
        $kop = mb_substr($price, $point + 1);
    }
    //����������� �����
    $str = write_number_in_words($rub);
    //����� ������(�,�)
    $word = " ������";
    //��������� �����
    $last_digit = $rub[(strlen($rub) - 1)];
    //������������� �����
    $pred_last_digit = $rub[(strlen($rub) - 2)];
    if ($last_digit == '1' && $pred_last_digit != '1')
        $word = " �����";
    elseif (($last_digit == '2' || $last_digit == '3' || $last_digit == '4') && $pred_last_digit != '1')
        $word = " �����";
    $str .= $word;
    //����������� �������
    if (!empty($kop)) {

        $str .= write_number_in_words($kop, 'femininum');
        //����� ������� (�, ��)
        $word = " ������";
        //��������� �����
        $last_digit = $kop[(strlen($kop) - 1)];
        //������������� �����
        $pred_last_digit = $kop[(strlen($kop) - 2)];
        if ($last_digit == '1' && $pred_last_digit != '1')
            $word = " �������";
        elseif (($last_digit == '2' || $last_digit == '3' || $last_digit == '4') && $pred_last_digit != '1')
            $word = " �������";
        $str .= $word;
    }
    return $str;
}

/**
 * ���������� ����� ��������
 * @author runcore
 * @uses morph(...)
 */
function num2str($num)
{
    $nul = '����';
    $ten = array(
        array('', '����', '���', '���', '������', '����', '�����', '����', '������', '������'),
        array('', '����', '���', '���', '������', '����', '�����', '����', '������', '������'),
    );
    $a20 = array('������', '�����������', '����������', '����������', '������������', '����������', '�����������', '����������', '������������', '������������');
    $tens = array(2 => '��������', '��������', '�����', '���������', '����������', '���������', '�����������', '���������');
    $hundred = array('', '���', '������', '������', '���������', '�������', '��������', '�������', '���������', '���������');
    $unit = array( // Units
        array('�������', '�������', '������', 1),
        array('�����', '�����', '������', 0),
        array('������', '������', '�����', 1),
        array('�������', '��������', '���������', 0),
        array('��������', '��������', '����������', 0),
    );
    //
    list($rub, $kop) = explode('.', sprintf("%015.2f", floatval($num)));
    $out = array();
    if (intval($rub) > 0) {
        foreach (str_split($rub, 3) as $uk => $v) { // by 3 symbols
            if (!intval($v)) continue;
            $uk = sizeof($unit) - $uk - 1; // unit key
            $gender = $unit[$uk][3];
            list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
            // mega-logic
            $out[] = $hundred[$i1]; # 1xx-9xx
            if ($i2 > 1) $out[] = $tens[$i2] . ' ' . $ten[$gender][$i3]; # 20-99
            else $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
            // units without rub & kop
            if ($uk > 1) $out[] = morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
        } //foreach
    } else $out[] = $nul;
    $out[] = morph(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub
    $out[] = $kop . ' ' . morph($kop, $unit[0][0], $unit[0][1], $unit[0][2]); // kop
    return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
}

/**
 * �������� ����������
 * @ author runcore
 */
function morph($n, $f1, $f2, $f5)
{
    $n = abs(intval($n)) % 100;
    if ($n > 10 && $n < 20) return $f5;
    $n = $n % 10;
    if ($n > 1 && $n < 5) return $f2;
    if ($n == 1) return $f1;
    return $f5;
}

/**
 *������� ��� ���� �� �������
 **/
function getRusMonth($month)
{
    if ($month > 12 || $month < 1) return FALSE;
    $aMonth = array('������', '�������', '�����', '������', '���', '����', '����', '�������', '��������', '�������', '������', '�������');
    return $aMonth[$month - 1];
}

/*****            �������� ������������� ������            *******/
if (amount_bd($_SESSION['id_users'], 'check_users') != false && isset($_POST['cech']) && isset($_POST['id_check'])) {
    global $db;
    mb_internal_encoding("UTF-8");
    $not_sfr = "ghalsderot123oyjlsdf";
//�������� �������� �� cach
//if(crypt($not_sfr ,$_POST['cech'])!=$_POST['cech'])exit('error');											
// ������ ������������
    $tableHeaderTopTextColour = array(255, 255, 255);
    $tableHeaderTopFillColour = array(125, 152, 179);
    $tableBorderColour = array(50, 50, 50);
    $tableRowFillColour = array(255, 255, 255);

    $logoFile = "../img/logo.png";
    $logoXPos = 10;
    $logoYPos = 2;
    $logoWidth = 50;

    $columnLabels = array("�", "�������", "�����", "����", "���-��", "�����");
    $columnWidth = array("8", "28", "98", "24", "13", "24");
    $columnHead = 5;


    $chartColours = array(
        array(255, 100, 100),
        array(100, 255, 100),
        array(100, 100, 255),
        array(255, 255, 100),
    );
    $id_check = escape(htmlspecialchars($_POST['id_check']));
    $data = amount_bd($id_check, 'check') or die ('error');
    $users = amount_bd((float)$data[0]['user_selling'], 'users') or die ('error');
    $con = amount_bd((float)$data[0]['id_company_inn'], 'counterparties') or die ('error');
// ����� ������������


    /**
     * ������� ��������� ��������
     **/

    $pdf = new PDF_MC_Table('P', 'mm', 'A4');
    $pdf->AddFont('ArialMT', '', 'arial.php'); //���������� ������ ������
    $pdf->AddFont('Arial-BoldMT', '', 'arial_bold.php'); //���������� ������ ������
    $pdf->AddFont('Times', '', 'times.php'); //���������� ������ ������
    $pdf->AddFont('Tahoma', '', 'tahoma.php'); //���������� ������ ������
    $pdf->AddPage();
    $pdf->SetFont('Times', '', 12);
// �������
    $pdf->Image($logoFile, $logoXPos, $logoYPos, $logoWidth);
    $pdf->Ln(-8);
    $pdf->Cell(110);
    $pdf->MultiCell(75, $columnHead, iconv("utf-8", "cp1251", (escape(htmlspecialchars($con[0]['full_name'])))) . ' ���: ' . (float)$data[0]['id_company_inn']);
    $pdf->Ln(0);
    $pdf->Cell(110);
    //����� ��������
    $number_phone = sprintf("%s (%s) %s-%s-%s", mb_substr((float)$con[0]['phone_number'], 0, 1), mb_substr((float)$con[0]['phone_number'], 1, 3), mb_substr((float)$con[0]['phone_number'], 4, 3), mb_substr((float)$con[0]['phone_number'], 7, 2), mb_substr((float)$con[0]['phone_number'], 9, 2));
    $pdf->MultiCell(80, $columnHead, iconv("utf-8", "cp1251", (escape(htmlspecialchars($con[0]['actual_address'])))) . ', ���. ' . $number_phone);
    $pdf->Ln();
    $pdf->Cell(50);
    $pdf->SetFont('Times', '', 16);
    $date = sprintf("%s %s %s", mb_substr($data[0]['date_selling'], 8, 2), getRusMonth(mb_substr($data[0]['date_selling'], 5, 2)), mb_substr($data[0]['date_selling'], 0, 4));

    $pdf->Cell(11, 12, '�������� ��� � ' . (float)$data[0]['id_check'] . ' �� ' . $date . ' �.');
    $pdf->SetFont('Times', '', 12);

    $pdf->Ln(-3);
    $pdf->SetTextColor($textColour[0], $textColour[1], $textColour[2]);


    /**
     * ������� �������
     **/

    $pdf->SetDrawColor($tableBorderColour[0], $tableBorderColour[1], $tableBorderColour[2]);
    $pdf->Ln(15);

// ���� ������ ���������
    $pdf->SetTextColor($tableHeaderTopTextColour[0], $tableHeaderTopTextColour[1], $tableHeaderTopTextColour[2]);
// ���� ������� �����
    $pdf->SetFillColor($tableHeaderTopFillColour[0], $tableHeaderTopFillColour[0], $tableHeaderTopFillColour[2]);

//	#### ����� ������� ####
    for ($i = 0; $i < count($columnLabels); $i++) {
        $pdf->Cell($columnWidth[$i], 12, $columnLabels[$i], 1, 0, 'C', true);
    }
    $pdf->Ln(12);

// ������� ������ � �������

    $fill = false;
    $row = 0;
    // Create the data cells
    $pdf->SetTextColor($textColour[0], $textColour[1], $textColour[2]);
    $pdf->SetFillColor($tableRowFillColour[0], $tableRowFillColour[1], $tableRowFillColour[2]);
    //$pdf->Ln(5);

    $pdf->SetWidths(array(8, 28, 98, 24, 13, 24));

    foreach ($data as $val) {
        // $pdf->Ln(-5);
// �
        $number_commodity = iconv("utf-8", "cp1251", (escape(htmlspecialchars((number_format($val['number_commodity'], 0, ',', ''))))));
// �����-���
        $bar_code = iconv("utf-8", "cp1251", (escape(htmlspecialchars(($val['bar_code'])))));
// �������	  
        $id_article = iconv("utf-8", "cp1251", (escape(htmlspecialchars(($val['id_article'])))));
// �����	 
        $name = iconv("utf-8", "cp1251", (escape(htmlspecialchars(($val['name_commodity'])))));
// ���� 
        $price_selling = iconv("utf-8", "cp1251", (escape(htmlspecialchars((number_format($val['price_selling'], 0, ',', ' '))))));
// ���-��
        $amount_celling = iconv("utf-8", "cp1251", (escape(htmlspecialchars((number_format($val['amount_celling'], 0, ',', ''))))));
// �����
        $summa = iconv("utf-8", "cp1251", (escape(htmlspecialchars((number_format(((float)$val['price_selling'] * (float)$val['amount_celling']), 0, ',', ' '))))));
// ������ �������
        $pdf->Row(array($number_commodity, $id_article, $name, $price_selling, $amount_celling, $summa), array('C', 'C', 'L', 'L', 'C', 'L'));
    }
    $discount = round((float)$data[0]['discount']);
    $itogo = round((float)$data[0]['price']);
    $summa = round((float)$data[0]['price_total']);
    $kol = round((int)$data[0]['number_items']);


    $pdf->Ln(0);
    $pdf->Cell(1);
    $pdf->MultiCell(130, $columnHead, '����� ������������ � ������ ' . $kol . ', �� ����� ' . number_format($summa, 0, ',', ' ') . ' ���.');

    $pdf->Ln(-$columnHead);
    $pdf->Cell(152);
    $pdf->Cell($columnHead, $columnHead, '�����: ' . number_format($itogo, 0, ',', ' ') . ' ���.', '0', '0', 'L');

    $pdf->Ln($columnHead);
    $pdf->Cell(152);
    $pdf->Cell($columnHead, $columnHead, '������: ' . number_format($discount, 0, ',', ' ') . ' ���.', '0', '0', 'L');

    $pdf->Ln($columnHead);
    $pdf->Cell(152);
    $pdf->Cell($columnHead, $columnHead, '�����: ' . number_format($summa, 0, ',', ' ') . ' ���.', '0', '0', 'L');

    $pdf->Ln(-$columnHead);
    $pdf->Cell(1);
    $pdf->MultiCell(130, $columnHead, num2str($summa));
    $pdf->Ln(20);
    $inisial = iconv("utf-8", "cp1251", (escape(htmlspecialchars($users[0]['surnames']))) . ' ' . (escape(htmlspecialchars($users[0]['nam']))) . ' ' . (escape(htmlspecialchars($users[0]['patronymic']))));
    $pdf->Cell(130, $columnHead, iconv("utf-8", "cp1251", (escape(htmlspecialchars($users[0]['fun'])))) . ', ' . $inisial);
    $pdf->Ln($columnHead);
    $pdf->Cell(-2);
    $pdf->Cell(100, 0, '', 1, 1, 'LRBT', 1);
    $pdf->SetFont('Times', '', 10);
    $pdf->Cell(20);
    $pdf->Cell(130, $columnHead, '(���������, �.�.�. ��������)');
    $pdf->Ln(0);
    $pdf->Cell(140);
    $pdf->Cell(50, 0, '', 1, 1, 'LRBT', 1);
    $pdf->Ln(0);
    $pdf->Cell(155);
    $pdf->Cell(130, $columnHead, '(�������)');

    /***
     * ������� PDF
     ***/

//Output the document F means save to server, F for download window popup
    $pdf->Output('check_pdf/temp_pdf/report-' . $_SESSION['id_users'] . '.pdf', 'F');
    $url = "report-" . $_SESSION['id_users'] . ".pdf";
    echo 'ok';
    mysqli_close($db);
    exit;
} else {
    header("Location: ./../index.php");
}

?>

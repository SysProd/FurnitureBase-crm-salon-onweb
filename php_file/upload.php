<?php

if ($_POST["ASPSESSID"] == 'asdasd123109oijflsdgk30' && isset($_FILES)) {
    $dir = '/upload_file/commodity/file/';
    $uploaddir = $_SERVER["DOCUMENT_ROOT"] . $dir;
    $uploaddir = str_replace('//', '/', $uploaddir);

    $allowedExt = array('xls', 'ods');
    $maxFileSize = 1 * 1024 * 1024; //1 MB

    $file = $uploaddir . basename(iconv('utf-8', 'cp1251', $_FILES['Filedata']['name']));
    $size = $_FILES['Filedata']['size'];

//проверяем размер и тип файла
    $name_file = explode('.', strtolower($_FILES['Filedata']['name']));
    $ext = end($name_file);
    $files = $uploaddir . basename(date('d-m-Y-H.i.s') . '.' . $ext);
    if (!in_array($ext, $allowedExt)) {
        echo "error format file";
        unlink($_FILES['Filedata']['tmp_name']);
        exit;
    }
    if ($maxFileSize < $size) {
        echo "error file size > 1 MB";
        unlink($_FILES['Filedata']['tmp_name']);
        exit;
    }
    if (move_uploaded_file($_FILES['Filedata']['tmp_name'], $files)) {
        //echo "success";
//	print_r($_SERVER);
        echo '<a href="' . $dir . basename(date('d-m-Y-H.i.s') . '.' . $ext) . '" id="files_names">' . basename(date('d-m-Y-H.i.s') . '.' . $ext) . '</a>';
    } else {
        echo "error " . $_FILES['Filedata']['error'] . " --- " . $_FILES['Filedata']['tmp_name'] . " %%% " . $file . "($size)";
        exit;
    }
} else {
    header("Location: ./../index.php");
}
?>
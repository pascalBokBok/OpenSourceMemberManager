<?php 
require_once "backend/database.php";

if (isset($_FILES['importFile'])) {
    $delim = isset($_POST['delimiter']) ? $_POST['delimiter'] : ';';
    $rawData = file ($_FILES['importFile']['tmp_name']);
    $data = array ();
    foreach($rawData as $line){
        $data[] = str_getcsv(iconv('ISO-8859-15','UTF-8//TRANSLIT',$line), $delim);
    }
    if (count($data)) {
        if (importCsv($data)){
            exit('<meta http-equiv="refresh" content="0; url=./">');
        } else {
            exit("No success.");
        }
    } else {
        throw new Exception("Invalid import format");
    }
}

?>
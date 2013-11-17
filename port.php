<?php 
require_once "backend/database.php";

if (isset($_FILES['importFile'])) {
    try {
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
    } catch (Exception $e){
        exit($e->getMessage());
    }
} else if($_REQUEST["action"]=="export"){
    $csv = exportCsv();
    $csv = iconv('UTF-8','ISO-8859-15//TRANSLIT',$csv);
    header('Content-Description: File Transfer');
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename='.date('Y-m-d H:i').' Member database.csv');
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: private');
    header('Pragma: public');
    header('Content-Length: ' . mb_strlen($csv, '8bit')/*$fileStat["size"]*/);
    ob_clean();
    echo $csv;
    flush();
}

?>
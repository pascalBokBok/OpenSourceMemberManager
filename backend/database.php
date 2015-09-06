<?php

function initialize($db){
    require_once "backend/SchemaLoader.php";
    $dbSchema = SchemaLoader::createDatabaseSchemaSQL();
    if ($db->exec($dbSchema) ===FALSE){
        throw new Exception ("Initializing database schema failed: ".$db->lastErrorMsg());
    }
}

function connect(){
    /** DB path is relative to api.php */
    if ($db = new SQLite3('backend/db.sqlite3',SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE)){
        $tableCheck = $db->query("SELECT name FROM sqlite_master WHERE name='members'");
        if ($tableCheck->fetchArray() === FALSE){
            initialize($db);
        }
        return $db;
    } else {
        throw new Exception ("No DB connection: ".$error_message);
    }
}

function returnDBError($db){
  return 'Database error '.$db->lastErrorCode().': '.$db->lastErrorMsg();
}

function getMembers($id=null){
    $db = connect();
    $q="Select * from members";
    if ($id!=null)
        $q .= ' where id='.(int)$id;
    $res = $db->query($q);
    if ($res==false){
        throw new Exception("Could not get memberlist. \n".returnDBError($db));
    }
    $out = array();
    while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
        $out[] = $row;
    }
    return $out;
}

function addNewMember($member){
    require_once "backend/SchemaLoader.php";
    $db = connect();
    $schema = SchemaLoader::getSchema();
    $memberFields = $schema->data_structures->members->fields;
    $columnNames = array();
    $fieldValues = array();
    foreach ($memberFields as $f){
        if (in_array("autoincrement",$f->properties))
            continue;//skip
        $columnNames[] = "'".$f->name."'";
        $fieldValues[] = "'".$db->escapeString($member[$f->name])."'";
    }
    $result = $db->exec("insert  into Members(".implode($columnNames,',').") 
        values (".implode($fieldValues,',').")");
    if (!$result)
        throw new Exception("Could not add member. \n".returnDBError($db));
}

function updateMember($member){
    $db = connect();
    $id = (int) $member["id"];
    require_once "backend/SchemaLoader.php";
    $schema = SchemaLoader::getSchema();
    $memberFields = $schema->data_structures->members->fields;
    $data = array();
    foreach ($memberFields as $f){
        if ($f->name == 'id')
            continue;
        $col = "'".$f->name."'";
        if ( isset($member[$f->name]) ){
            $data[] .= $col."='".$db->escapeString($member[$f->name])."'";
        } else{
            $data[] .= $col.'=null';
        }
    }
    $sql = "UPDATE members SET ".implode($data,', ')." where id=".$id;
    $result = $db->exec($sql);
    if (!$result)
        throw new Exception("Could not execute update member. \n".returnDBError($db));
    if ($db->changes()==0){
        throw new Exception("No change in database");
    }
}

function deleteMember($id){
    $db = connect();
    $id = (int) $id;
    $result = $db->exec("delete from Members where id=".$id);
    if (!$result)
        throw new Exception("Could not delete member. \n".returnDBError($db));
    if ($db->changes()==0){
        throw new Exception("No change in database");
    }
}
function getMemberFields(){
    require_once "backend/SchemaLoader.php";
    $schema = SchemaLoader::getSchema();
    $memberFields = $schema->data_structures->members->fields;
    $dataFields   = Array();
    foreach ($memberFields as $f){
        $dataFields[] = $f->name;
    }
    return $dataFields;
}

function str_putcsv($input, $delimiter = ',', $enclosure = '"') {
    $fp = fopen('php://temp', 'r+');
    fputcsv($fp, $input, $delimiter, $enclosure);
    rewind($fp);
    $data = fread($fp, 9999999);
    fclose($fp);
    return $data;
}
function exportCsv(){
    $memberFields = getMemberFields();
    $csv = str_putcsv($memberFields,';');
    $db = connect();
    $sql = 'Select "'.implode($memberFields,'","').'" from members;';
    $members = $db->query($sql);
    while ($member = $members->fetchArray(SQLITE3_NUM)) {
        $csv .= str_putcsv($member,';');
    }
    return $csv;
}

function importCsv($data) {
    $db = connect();
    $importFields = array_shift($data);
    $importMap    = Array();
    require_once "backend/SchemaLoader.php";
    $schema = SchemaLoader::getSchema($assoc=true);
    $databaseFields = $schema["data_structures"]["members"]["fields"];
    $dataFields   = Array();
    foreach ($databaseFields as $dbField) {
        $dataFields[ strtolower($dbField['name']) ] = 1;
    }
    foreach ($importFields as $i => $fieldName) {
        if (isset($dataFields[ strtolower($fieldName) ])) {
            $importMap[ strtolower($fieldName) ] = $i;
        }
    }
    if (count( $importFields) == 0) {
        throw new Exception("No fields matched");
    }
    $importFields = array_keys(  $importMap );
    $sql = '';
    foreach ( $data as $row ) {
        $values = Array();
        foreach ($importFields as $fieldName) {
            $escapedValue = isset($row[$importMap[ $fieldName ]]) ? $db->escapeString($row[ $importMap[ $fieldName ]]) : '';
            array_push($values, $escapedValue);
        }
        $sql .= "INSERT INTO members ('" . implode($importFields, "', '") . "') VALUES ('" . implode( $values, "', '") . "');";
    }
    $result = $db->exec($sql);
    if (!$result)
        throw new Exception("Could not import. \n".returnDBError($db));
    return true;
}
?>
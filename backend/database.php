<?php

function initialize($db){
    require_once 'data.php';
    $db_schema_members = createDatabaseTables($dataJSON,$fieldType2sqliteType);
    if ($db->exec($db_schema_members) ===FALSE){
        throw new Exception ("Initializing database schema failed: ".$db->lastErrorMsg());
    }
}

function connect(){
    if ($db = new SQLite3('db.sqlite3',SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE)){
        $tableCheck = $db->query("SELECT name FROM sqlite_master WHERE name='members'");
        if ($tableCheck->fetchArray() === FALSE){
            initialize($db);
        }
        return $db;
    } else {
        throw new Exception ("No DB connection: ".$error_message);
    }
}

function getMembers($id=null){
    $db = connect();
    $q="Select * from members";
    if ($id!=null)
        $q .= ' where id='.(int)$id;
    $res = $db->query($q);
    if ($res==false){
        throw new Exception('Could not get memberlist:'.$db->lastErrorMsg());
    }
    $out = array();
    while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
        $out[] = $row;
    }
    return $out;
}

function addNewMember($member){
    $db = connect();
    require_once 'data.php';
    $memberFields = json_decode($memberDataFieldsJSON);
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
        throw new Exception("Could not add member");
}

function updateMember($member){
    $db = connect();
    $id = (int) $member["id"];
    require_once 'data.php';
    $memberFields = json_decode($memberDataFieldsJSON);
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
        throw new Exception("Could not execute update member");
    if ($db->changes()==0){
        throw new Exception("No change in database");
    }
}

function deleteMember($id){
    $db = connect();
    $id = (int) $id;
    $result = $db->exec("delete from Members where id=".$id);
    if (!$result)
        throw new Exception("Could not execute 'delete member':".$db->lastErrorMsg());
    if ($db->changes()==0){
        throw new Exception("No change in database");
    }
}
function getMemberFields(){
    require_once 'data.php';
    $databaseFields = json_decode($memberDataFieldsJSON,$assoc=true);
    $dataFields   = Array();
    foreach ($databaseFields as $dbField) {
        $dataFields[ strtolower($dbField['name']) ] = 1;
    }
    return $dataFields;
}

function importCsv($data) {
    $db = connect();
    $importFields = array_shift($data);
    $importMap    = Array();
    $dataFields = getMemberFields();
    foreach ($importFields as $i => $fieldName) {
        if (isset($dataFields[ strtolower($fieldName) ])) {
            $importMap[ strtolower($fieldName) ] = $i;
        }
    }
    if (count( $importFields) == 0) {
        throw new Exception("No fields matched");
    }
    $importFields = array_keys(  $importMap );
    $sql = 'BEGIN TRANSACTION;';
    foreach ( $data as $rowno => $row ) {
        $sql .= "INSERT INTO members (" . implode($importFields, ", ") . ") VALUES ('";
        $values = Array();
        foreach ($importFields as $fieldName) {
            array_push($values, $db->escapeString($row[ $importMap[ $fieldName ]]));
        }
        $sql .= implode( $values, "', '") . "');";
    }
    $sql .= "END TRANSACTION";
    $result = $db->exec($sql);
    if (!$result)
        throw new Exception("Could not insert row $rowno:".$db->lastErrorMsg());
    return true;
}
?>
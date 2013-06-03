<?php

function initialize($db){
    require_once 'data.php';
    $db_schema_members = createDatabaseTable($memberDataJSON,$fieldType2sqliteType);
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

function getAllMembers(){
    $db = connect();
    $res = $db->query("Select * from members");
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
    if (is_numeric($member["id"])){
        $member["id"] = $member["id"];
    }
    $id = (int) $id;
    $result = $db->exec("UPDATE members SET name='".$db->escapeString($member["name"])."',
        email_address='".$db->escapeString($member["email_address"])."' where id=".$member["id"]);
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

?>
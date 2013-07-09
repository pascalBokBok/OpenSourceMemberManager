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

function importCsv() {
	$filename = $_FILES['importFile']['tmp_name'];
	$res = '';
	if ($filename) {
		$delim = $_POST['delimiter'] or ';';
		$handle = fopen($filename, "r");
		$data = fgetcsv($handle, 0, $delim);
		
		if ($data) {
			$db = connect();
			$importFields = shift($data);
			$databaseFields = json_decode($memberDataFieldsJSON);
			$dataFields   = Array();
			$importMap    = Array();
			foreach ($databaseFields as $dbField) {
				$dataFields[ $dbField['name'] ] = 1;
			}
			
			foreach ($importFields as $i => $fieldname) {
				if ($dataFields[ $fieldname]) {
					$importMap[ $fieldname ] = $i;
				}
			}
				
			if (count( $importFields) == 0) {
				throw new Exception("No fields matched");
			}
			
			$importFields = array_keys(  $importMap );
			
			$sql = 'BEGIN TRANSACTION;';
			foreach ( $data as $rowno => $row ) {
				$sql .= "INSERT INTO members (" . implode($importFields, ", ") . ") VALUES (";
				$values = Array();
				foreach ($importFields as $fieldName) {
					array_push($values, $db->escapeString($row[ $importMap[ $fieldName ]]));
				}
				$sql .= implode( $values, ", ") . ");";				
			}
			$sql .= "END TRANSACTION";
			$result = $db->exec($sql);
			if (!$result)
        		throw new Exception("Could not insert row $rowno:".$db->lastErrorMsg());							
		} else {
			throw new Exception("Invalid import format");
		}
	}
	return $res;
}
?>
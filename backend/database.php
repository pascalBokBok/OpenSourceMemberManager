<?php
require_once 'dbEncryptionKey.php';

function initialize($db){
	require_once '../database_schema/database.php';
	if ($db->exec($db_schema_members) ===FALSE){
		die ("Initializing database schema failed: ".$db->lastErrorMsg());
	}
}

function connect(){
	global $dbEncryptionKey;
	if ($db = new SQLite3('db.sqlite3',SQLITE3_OPEN_READWRITE | SQLITE3_OPEN_CREATE,$dbEncryptionKey)){
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
	$out = array();
	while ($row = $res->fetchArray(SQLITE3_ASSOC)) {
		$out[] = $row;
	}
	return $out;
}

function addNewMember($member){
	$db = connect();
	$result = $db->exec("insert  into Members(name,email_address) 
		values ('".$db->escapeString($member["name"])."',
		'".$db->escapeString($member["email_address"])."')");
	if (!$result)
		exit("Could not save user");
}

function updateMember($id,$memberInfoJson){
	
}

?>
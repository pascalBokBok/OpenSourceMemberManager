<?php
function initialize($db){
	if ($db->querySingle("Select Id from members")===FALSE){
		/* no rows in members */
		$db->exec("Drop table Members");
		require_once '../database_schema/database.php';
		if ($db->exec($db_schema_members) ===FALSE){
			die ("Initializing database schema failed: ".$db->lastErrorMsg());
		}
	} else {
		die("Initialize called on non-empty database. Aborting.");
	}
}

function connect(){
	if ($db = new SQLite3('members.sqlite3')){
		if ($db->exec('CREATE TABLE members (test)')){
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
	while ($row = $res->fetchArray()) {
		$out[] = $row;
	}
	return json_encode($out);
}

?>
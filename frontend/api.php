<?php
if (!isset($_GET["action"])){
	exit("You need to specify an action.");
}

require_once "../backend/database.php";
if ($_GET["action"]=="getMemberList"){
	exit(json_encode(getAllMembers()));
} else if ($_GET["action"]=="addNewMember"){
	$db = connect();
	$member = $_GET;
	unset($member['action']);
	addNewMember($member);
} else {
	exit("Action not known");
}

?>
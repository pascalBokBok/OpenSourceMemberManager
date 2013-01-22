<?php
require_once 'database.php';
$db = connect();
$added = $db->exec("insert into Members values (null,'Pascal','pascal@example.com','2012')");
if ($added){
	echo 'Added Pascal to Members<br>';
} else {
	echo "Failed to add Pascal:".$db->lastErrorMsg();
}
echo getAllMembers();
?>
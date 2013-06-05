<?php
try {
    $response = array("payload"=>null);
    if (!isset($_GET["action"])){
        throw new Exception("You need to specify an action.");
    }

    require_once "backend/database.php";
    if ($_GET["action"]=="getMemberList"){
        $payload = getMembers();
    } else if ($_GET["action"]=="addNewMember"){
        $db = connect();
        $member = $_GET;
        unset($member['action']);
        addNewMember($member);
    } else if ($_GET["action"]=="updateMember"){
        $db = connect();
        $member = $_GET;
        unset($member['action']);
        updateMember($member);
    } else if ($_GET["action"]=="deleteMember"){
        deleteMember($_GET['id']);
    } else if ($_GET["action"]=="getMemberFields"){
        require_once 'backend/data.php';
        $payload = $memberDataFieldsJSON;
    } else if ($_GET["action"]=="getMember"){
        $members = getMembers($_GET['id']);
        $payload = $members[0];
    } else {
        throw new Exception("Action not known");
    }
    $response["error"] = false; 
} catch (Exception $e){
    $response["error"] = true;
    $response["error_msg"] = $e->getMessage();
    $response["error_code"]= $e->getCode();
}
$response["payload"] = isset($payload) ? $payload : null;
exit(json_encode($response));
?>
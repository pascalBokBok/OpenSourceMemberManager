<?php
/** This API is the only part of the backend that is visible from the front end.
    Therefore it acts as a gatekeeper.
 */
session_start();
$response = array("payload"=>null);
if (!isset($_GET["action"])){
    throw new Exception("You need to specify an action.");
}
$action = $_REQUEST["action"];
try {    
    if ($action=="authenticate"){
        require_once "backend/security.php";
        authenticate();
    }
    
    if (!isset($_SESSION["authenticated"]) or $_SESSION["authenticated"]!=true){
        Throw new Exception("You are not logged in.",401); //code 401 (unauthorized) copied from http protocol
    }
    require_once "backend/database.php";
    
    if ($_SESSION["authenticated"] and  $action!="authenticate"){
        switch ($action){
            case "getMemberList":
                $payload = getMembers();
                break;
            case "addNewMember":
                $member = $_GET;
                unset($member['action']);
                addNewMember($member);
                break;
            case "updateMember":
                $member = $_GET;
                unset($member['action']);
                updateMember($member);
                break;
            case "deleteMember":
                deleteMember($_GET['id']);
                break;
            case "getMemberFields":
                $dataStructure = json_decode(file_get_contents("backend/data_structure.json"));
                $payload = $dataStructure->data_structures->members;
                break;
            case "getMember":
                $members = getMembers($_GET['id']);
                $payload = $members[0];
                break;
            case "importCsv":
                $payload = importCsv();
                break;
            case "testAuthenticated":
                $payload = true;
                break;
            default:
                throw new Exception("Action not known");
        }
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
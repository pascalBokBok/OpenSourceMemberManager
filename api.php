<?php
/** This API is the only part of the backend that is visible from the front end.
    Therefore it acts as a gatekeeper.
 */
session_start();
$response = array('error'=>false,'payload'=>null);
try {    
    if (!isset($_GET["action"])){
        throw new Exception("You need to specify an action.");
    }
    $action = $_REQUEST["action"];
    require_once "backend/security.php";
    if ($action=="authenticate"){
        authenticate();
    }
    errorIfNotAuthenticated();
    
    require_once "backend/database.php";
    
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
            require_once "backend/SchemaLoader.php";
            $schema = SchemaLoader::getSchema();
            $payload = $schema->data_structures->members->fields;
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
        case "authenticate":
            //work already done
            break;
        default:
            throw new Exception("Action not known");
    }
} catch (Exception $e){
    $response["error"] = true;
    $response["error_msg"] = $e->getMessage();
    $response["error_code"]= $e->getCode();
    $response["stack"]= $e->getTraceAsString();
}
$response["payload"] = isset($payload) ? $payload : null;
exit(json_encode($response));
?>

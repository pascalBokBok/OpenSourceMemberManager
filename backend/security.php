<?php

function authenticate(){
    //disabled for now; use htaccess
    $_SESSION["authenticated"] = true;
    return;
    if ($_GET["password"]=="hardcodedsecurity:-)"){
        $_SESSION["authenticated"] = true;
    } else {
        $_SESSION["authenticated"] = false;
        throw new Exception ("Incorrect login");
    }
}

// function doesHtaccessWork(){
//     /** Assume that request is for api.php in app root */
//     $protocol = ($_SERVER['HTTPS']==0?'http':'https')."://";
//     $appRoot = $protocol.$_SERVER['HTTP_HOST'].dirname($_SERVER['REQUEST_URI']);
//     $testResult = http_get($appRoot.'/backend/db.sqlite3');
//     if ($testResult['response_code']==403){/** 403 forbidden*/
//         return true;
//     } else {
//         return false;
//     }
// }

?>
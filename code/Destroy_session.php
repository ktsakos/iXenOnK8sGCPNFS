<?php
$_SESSION = array();
$_SESSION["access_token"]= array();
$_SESSION["USERNAME"]= array();
$_SESSION["PASSWORD"]= array();
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();
echo "session_destroyed";
?>

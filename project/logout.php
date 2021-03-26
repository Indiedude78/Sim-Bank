<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
//since this function call is included we can omit it here. Having multiple calls to session_start() will cause errors/warnings
//session_start();
// remove all session variables
session_unset();
// destroy the session
session_destroy();
echo "<br><h3>Logged out</h3>";
die(header("refresh:2;url:login.php")); //redirect to login page
//echo "<pre>" . var_export($_SESSION, true) . "</pre>";

?>

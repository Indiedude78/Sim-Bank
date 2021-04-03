<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
//since this function call is included we can omit it here. Having multiple calls to session_start() will cause errors/warnings
//session_start();
// remove all session variables
session_unset();
// destroy the session
session_destroy();
flash("You have been logged out, redirecting...");
header("refresh:2;url=login.php"); //redirect to index page
//echo "<pre>" . var_export($_SESSION, true) . "</pre>";
//Debug messages are commented out

?>
<?php require(__DIR__ . "/partials/flash.php"); ?>

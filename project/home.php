<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
//we use this to safely get the email to display
$email = "";
$username = "";
if (is_logged_in()) { //Secuirty check to see if user is logged in
    $username = get_username();
    $email = get_email();
    flash("Hello $username");
}
?>
<?php 
    require_once(__DIR__ . "/partials/dashboard.php");
?>
<?php
if (!is_logged_in()) { //If user is not logged in, the can login from the home page
    die(header("Location: index.php"));
}
?>
<?php require(__DIR__ . "/partials/flash.php"); ?>
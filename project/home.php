<head>
    <title>Simulation Bank: Home</title>
</head>

<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php if (get_first_name() == null && get_last_name() == null) {
    require_once(__DIR__ . "/set_name.php");
}
?>
<?php
require_once(__DIR__ . "/partials/dashboard.php");
?>
<?php
if (!is_logged_in()) { //If user is not logged in, the can login from the home page
    header("Location: index.php");
}
?>
<?php require(__DIR__ . "/partials/flash.php"); ?>



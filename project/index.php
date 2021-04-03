<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php 
if (is_logged_in()) {
    header("Location: home.php");
}
?>

<h2>Welcome, please <a href="register.php">register</a> or <a href="login.php">login</a></h2>
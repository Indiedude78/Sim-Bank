<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
//we use this to safely get the email to display
$email = "";
$username = "";
if (is_logged_in()) {
    $username = get_username();
    $email = get_email();
}
?>
<h3>Hello<br> <?php if ($username != NULL) {echo $username;} else {echo $email;} ?></h3><br>
<?php
if (!is_logged_in()) {
    echo '<h3>Please <a href="register.php">register</a> or <a href="login.php">login</a> to continue</h3>';
}


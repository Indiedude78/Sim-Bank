<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
//we use this to safely get the email to display
$email = "";
$username = "";
if (is_logged_in()) { //Secuirty check to see if user is logged in
    $username = get_username();
    $email = get_email();
}
?>
<h3>Hello<br> <?php if ($username != NULL) {echo $username;} else {echo $email;} ?></h3><br>
<?php
if (!is_logged_in()) { //If user is not logged in, the can login from the home page
    die(header("Location: index.php"));
}
?>


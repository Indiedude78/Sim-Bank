<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
//we use this to safely get the email to display
$email = "";
$username = "";
if (is_logged_in()) {
    $username = get_username();
    $email = get_email();
    if ($username != NULL) {
        echo "<h3>Welcome, $username</h3>";
    }
    else {
        echo "<h3>Welcome, $email</h3>";
    }
}

?>


<?php require_once(__DIR__ . "/../lib/helpers.php"); ?>
<?php
    if (!is_logged_in()) {
        die(header("Location: login.php"));
    }
?>

<div class="dashboard-container">
    <ul id="dashboard">
        <li><a href="test_create_accounts.php">Create Account</a></li>
        <li><a href="#">View Accounts</a></li>
        <li><a href="#">Deposit</a></li>
        <li><a href="#">Withdraw</a></li>
        <li><a href="#">Transfer</a></li>
        <li><a href="profile.php">Profile</a></li>

    </ul>
</div>
<?php require_once(__DIR__ . "/../lib/helpers.php"); ?>
<?php
if (!is_logged_in()) {
    die(header("Location: login.php"));
}
?>

<div class="dashboard-container">
    <ul id="dashboard">
        <li><a href="test_create_accounts.php">Create Account</a></li>
        <li><a href="test_list_accounts.php">View Accounts</a></li>
        <li><a href="test_deposit_account.php">Deposit</a></li>
        <li><a href="test_withdraw_account.php">Withdraw</a></li>
        <li><a href="test_transfer_funds.php">Transfer</a></li>
        <li><a href="profile.php">Profile</a></li>

    </ul>
</div>
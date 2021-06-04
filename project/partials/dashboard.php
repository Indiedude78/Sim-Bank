<?php require_once(__DIR__ . "/../lib/helpers.php"); ?>
<?php
if (!is_logged_in()) {
    die(header("Location: login.php"));
}
?>
<div class="side-bar">
    <div id="side-icon-container">
        <button id="side-bar-button">
            <span class="material-icons">
                menu
            </span>
        </button>
    </div>
    <div class="dashboard-container">
        <h3>Account Services</h3>
        <ul id="dashboard">
            <li><a href="test_create_accounts.php">Create Account</a></li>
            <li><a href="test_list_accounts.php">View Accounts</a></li>
            <li><a href="test_deposit_account.php">Deposit</a></li>
            <li><a href="test_withdraw_account.php">Withdraw</a></li>
            <li><a href="test_transfer_funds.php">Transfer</a></li>
            <li><a href="test_ext_transfer_funds.php">External Transfer</a></li>
            <li><a href="test_loan_account.php">Loan</a></li>
            <li><a href="test_close_account.php">Close Account</a></li>

        </ul>
    </div>
</div>
<script src="jquery/jquery.js"></script>

<script src="static/js/sideBar.js"></script>
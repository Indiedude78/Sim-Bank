<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php if (!is_logged_in()) {die(header("Location: login.php"));} ?>
<?php 
    require_once(__DIR__ . "/partials/dashboard.php");
?>
<?php 
if (isset($_GET["id"]) && $_GET["id"] != 1) {
    $acc_id = $_GET["id"];
    $user_id = get_id();
    $db = getDB();
    $stmt = $db->prepare("SELECT Accounts.account_number, Accounts.account_type, balance_change, transaction_type, memo, Accounts.balance FROM Transactions JOIN Accounts ON Transactions.account_source = Accounts.id WHERE Accounts.id = :id and Accounts.user_id = :user_id");
    $r = $stmt->execute([":id"=>$acc_id, ":user_id"=>$user_id]);
    $e = $stmt->errorInfo();
    if ($e[0] != "00000") {
        flash("Something went wrong");
    }
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
}
else {
    flash("You do not have permission to do this!");
}
?>
<div class="transaction-information">
<table>
    <tr>
        <th>Account Number</th>
        <th>Account Type</th>
        <th>Deposit or Withdrawal</th>
        <th>Transaction Type</th>
        <th>Memo</th>
        <th>Current Balance</th>
    </tr>
    <?php if (isset($result)): ?>
        
            <th><?php safer_echo($result["account_number"]); ?></th>
            <th><?php safer_echo($result["account_type"]); ?></th>
            <th><?php safer_echo($result["balance_change"]); ?></th>
            <th><?php safer_echo($result["transaction_type"]); ?></th>
            <th><?php safer_echo($result["memo"]); ?></th>
            <th><?php safer_echo($result["balance"]); ?></th>
    <?php endif; ?>
</table>
</div>
<?php require(__DIR__ . "/partials/flash.php"); ?>
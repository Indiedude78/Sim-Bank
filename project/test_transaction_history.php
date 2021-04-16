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
    $stmt = $db->prepare("SELECT Accounts.account_number, Accounts.account_type, balance_change, transaction_type, memo, expected_total, Accounts.balance FROM Transactions JOIN Accounts ON Transactions.account_source = Accounts.id WHERE Accounts.id = :id and Accounts.user_id = :user_id LIMIT 10");
    $r = $stmt->execute([":id"=>$acc_id, ":user_id"=>$user_id]);
    $e = $stmt->errorInfo();
    if ($e[0] != "00000") {
        flash("Something went wrong");
    }
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
        <th>Balance Change</th>
    </tr>
    <?php if (isset($result)): ?>
        <?php foreach($result as $r): ?>
        <tr>
            <th><?php safer_echo($r["account_number"]); ?></th>
            <th><?php safer_echo($r["account_type"]); ?></th>
            <th><?php safer_echo($r["balance_change"]); ?></th>
            <th><?php safer_echo($r["transaction_type"]); ?></th>
            <th><?php safer_echo($r["memo"]); ?></th>
            <th><?php safer_echo($r["expected_total"]); ?></th>
        </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>
</div>
<?php require(__DIR__ . "/partials/flash.php"); ?>
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
<h3>Account Number:  <?php safer_echo($result[0]["account_number"]) ?></h3>
<h4>Account Type:  <?php safer_echo($result[0]["account_type"]) ?></h4>
<table>
<thead>
    <tr>
        <th>Balance Change</th>
        <th>Transaction Type</th>
        <th>Memo</th>
    </tr>
</thead>
    <?php if (isset($result)): ?>
        <?php foreach($result as $r): ?>
        <tbody>
            <tr>
                <td class="data-row"><?php safer_echo($r["balance_change"]); ?></td>
                <td class="data-row"><?php safer_echo($r["transaction_type"]); ?></td>
                <td class="data-row"><?php safer_echo($r["memo"]); ?></td>
            </tr>
        </tbody>
        <?php endforeach; ?>
        <tfoot>
        <tr>
            <td colspan="3" id="total"><?php safer_echo("$ " .$result[0]["balance"]) ?></td>
        </tr>
        </tfoot>
    <?php endif; ?>
</table>
</div>
<?php require(__DIR__ . "/partials/flash.php"); ?>
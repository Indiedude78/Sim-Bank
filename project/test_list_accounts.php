<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!has_role("Admin")) {
    flash("You do not have permission to view this page");
    die(header("Location: home.php"));
}
?>

<?php 
$acc_type = array("Checking", "Saving"); 
?>

<form class="user-reg" id="user-reg" method="GET">
    <label for="account_type">View Accounts</label>
    <select id="account_type" name="account_type" placeholder="Account Type">
        <option value="None">Choose Account Type</option>
        <?php 
            foreach ($acc_type as $acc) {
                echo "<option value='$acc'>$acc</option>";
            }
        ?>
    </select>
    <input type="submit" name="search" value="Search"/>
</form>

<?php
if (isset($_GET["search"])) {
    $query_acc = $_GET["account_type"];
    $user_id = get_id();
    if (isset($_GET["account_type"])) {
        $db = getDB();
        $stmt = $db->prepare("SELECT account_number, balance FROM Accounts WHERE account_type = :acc_type and user_id = :id LIMIT 5");
        $r = $stmt->execute([
            ":acc_type" => $query_acc,
            ":id" => $user_id
        ]);
        $e = $stmt->errorInfo();
        if ($e[0] != "00000") {
            flash("Something went wrong");
        }
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>
<?php if (isset($result)): ?> 
<div class="account-information">
    <div>
        <h3>Account Type:</h3>
        <h4><?php safer_echo($_GET["account_type"]); ?></h4>
    </div>
    <div>
        <?php foreach($result as $r): ?>
            <h5>Account Number:</h5>
            <div id="account_number"><?php safer_echo($r["account_number"]); ?></div>
            <h5>Account Balance:</h5>
            <div id="account_balance"><?php safer_echo($r["balance"]) ?></div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>
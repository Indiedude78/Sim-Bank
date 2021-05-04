<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php if (!is_logged_in()) {
    die(header("Location: login.php"));
} ?>
<?php
require_once(__DIR__ . "/partials/dashboard.php");
?>
<?php
$user_id = get_id();
if (isset($user_id)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT account_number, account_type FROM Accounts where user_id = :user_id");
    $r = $stmt->execute([
        ":user_id" => $user_id
    ]);
    $e = $stmt->errorInfo();
    if ($e[0] != "00000") {
        flash("Something went wrong");
    }
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<form class="user-reg" id="user-reg" method="GET">
    <label for="account_number">Choose Account</label>
    <select id="account_number" name="account_number" placeholder="Deposit">
        <option value="None">Choose Account to Deposit</option>
        <?php
        if (isset($result)) {
            foreach ($result as $r) {
                echo "<option value=" . $r["account_number"] . ">" . $r["account_number"] . "</option>";
            }
        }
        ?>
    </select>
    <input type="submit" id="submit" name="search" value="Search" />
</form>

<?php if (isset($_GET["search"]) && $_GET["account_number"] != "None") : ?>
    <form class="transaction-form" method="GET">
        <label for="acc_num"><?php safer_echo("Account: " . $_GET["account_number"]); ?></label><br><br>
        <input type="hidden" name="acc_num" id="acc_num" value="<?php safer_echo($_GET["account_number"]); ?>" />
        <label for="deposit_amount">Deposit Amount</label>
        <input type="number" id="deposit_amount" name="deposit_amount" min="1" step="any" placeholder="Deposit Amount" required />
        <label for="memo">Memo</label><br>
        <textarea id="memo" name="memo" rows="4" cols="30" placeholder="Memo"></textarea>
        <input type="submit" id="deposit" name="deposit" value="Deposit" />
    </form>
<?php endif; ?>

<?php
if (isset($_GET["deposit"])) {
    $acc_num = $_GET["acc_num"];
    $deposit_amount = $_GET["deposit_amount"];
    $memo = $_GET["memo"];
    if ($acc_num != "None") {
        $db = getDB();
        $stmt = $db->prepare("SELECT id, balance FROM Accounts WHERE account_number = :acc_num");
        $r = $stmt->execute([":acc_num" => $acc_num]);
        $e = $stmt->errorInfo();
        if ($e[0] != "00000") {
            flash("Something went wrong");
        }
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt2 = $db->prepare("SELECT id, balance FROM Accounts WHERE account_type = :acc_type");
        $r2 = $stmt2->execute([":acc_type" => "World"]);
        $e2 = $stmt2->errorInfo();
        if ($e2[0] != "00000") {
            flash("Something went wrong");
        }
        $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    }
    if (isset($result) && isset($result["id"])) {
        $db = getDB();
        $acc_src = $result2["id"];
        $acc_dest = $result["id"];
        $transaction_type = "deposit";
        $expected_total = $result2["balance"] + (-$deposit_amount);
        $stmt = $db->prepare("INSERT INTO Transactions(account_source, account_destination, balance_change, transaction_type, memo, expected_total) VALUES(:acc_src, :acc_dest, :balance_change, :transaction_type, :memo, :total)");
        $r = $stmt->execute([
            ":acc_src" => $acc_src,
            ":acc_dest" => $acc_dest,
            ":balance_change" => (-$deposit_amount),
            ":transaction_type" => $transaction_type,
            ":memo" => $memo,
            ":total" => $expected_total
        ]);
        $stmt = $db->prepare("UPDATE Accounts set balance = :balance where id = :acc_id");
        $stmt->execute([
            ":balance" => $expected_total,
            ":acc_id" => $acc_src
        ]);

        $acc_src = $result["id"];
        $acc_dest = $result2["id"];
        $transaction_type = "deposit";
        $expected_total = $result["balance"] + ($deposit_amount);
        $stmt = $db->prepare("INSERT INTO Transactions(account_source, account_destination, balance_change, transaction_type, memo, expected_total) VALUES(:acc_src, :acc_dest, :balance_change, :transaction_type, :memo, :total)");
        $r = $stmt->execute([
            ":acc_src" => $acc_src,
            ":acc_dest" => $acc_dest,
            ":balance_change" => $deposit_amount,
            ":transaction_type" => $transaction_type,
            ":memo" => $memo,
            ":total" => $expected_total
        ]);
        $stmt = $db->prepare("UPDATE Accounts set balance = :balance where id = :acc_id");
        $stmt->execute([
            ":balance" => $expected_total,
            ":acc_id" => $acc_src
        ]);

        if ($r) {
            flash("Deposit Successful!");
        } else {
            flash("Something went wrong!");
        }
    }
}
?>

<?php require(__DIR__ . "/partials/flash.php"); ?>
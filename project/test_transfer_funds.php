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
    $stmt = $db->prepare("SELECT account_number, account_type FROM Accounts where user_id = :user_id and closed = 0 and frozen = 0 AND account_type != 'Loan'");
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

<form class="user-reg" id="user-reg" method="POST">
    <label for="account_number_source">From:</label>
    <select id="account_number_source" name="account_number_source" placeholder="Source Account" required>
        <option value="" disabled selected>Choose Source Account</option>
        <?php
        if (isset($result)) {
            foreach ($result as $r) {
                echo "<option value=" . $r["account_number"] . ">" . $r["account_number"] . "</option>";
            }
        }
        ?>
    </select>
    <label for="account_number_destination">To:</label>
    <select id="account_number_destination" name="account_number_destination" placeholder="Destination Account" required>
        <option value="" disabled selected>Choose Destination Account</option>
        <?php
        if (isset($result)) {
            foreach ($result as $r) {
                echo "<option value=" . $r["account_number"] . ">" . $r["account_number"] . "</option>";
            }
        }
        ?>
    </select>
    <label for="amount">Transfer Amount:</label>
    <input type="number" id="amount" name="amount" min="1" step="any" placeholder="Enter Amount" required />
    <label for="memo">Memo:</label><br>
    <textarea id="memo" name="memo" rows="4" cols="30" placeholder="Memo"></textarea>
    <input type="submit" name="Submit" value="Transfer" />
</form>

<?php
if (isset($_POST["Submit"])) {
    $acc_src = $_POST["account_number_source"];
    $acc_dest = $_POST["account_number_destination"];
    $transfer_amount = $_POST["amount"];
    $transaction_type = "transfer";
    if (isset($_POST["memo"])) {
        $memo = $_POST["memo"];
    } else {
        $memo = '';
    }
    if (isset($acc_src) && isset($acc_dest) && isset($transfer_amount) && ($acc_src != $acc_dest)) {
        $db = getDB();
        $stmt = $db->prepare("SELECT id, balance FROM Accounts WHERE account_number = :acc_src");
        $r = $stmt->execute([
            ":acc_src" => $acc_src,
        ]);
        $e = $stmt->errorInfo();
        if ($e[0] != "00000") {
            flash("Something went wrong!");
        }
        $acc_info1 = $stmt->fetch(PDO::FETCH_ASSOC);
        $acc_src_id = $acc_info1["id"];
        $acc_src_balance = $acc_info1["balance"];
        if ($transfer_amount > $acc_src_balance) {
            flash("Insufficient funds in source account");
        } else {

            $stmt = $db->prepare("SELECT id, balance FROM Accounts WHERE account_number = :acc_dest");
            $r = $stmt->execute([
                ":acc_dest" => $acc_dest,
            ]);

            $acc_info2 = $stmt->fetch(PDO::FETCH_ASSOC);
            $acc_dest_id = $acc_info2["id"];
            $acc_dest_balance = $acc_info2["balance"];

            $acc_src_newBalance = $acc_src_balance - $transfer_amount;
            $acc_dest_newBalance = $acc_dest_balance + $transfer_amount;

            $stmt = $db->prepare("INSERT INTO Transactions(account_source, account_destination, balance_change, transaction_type, memo, expected_total) VALUES(:acc_src, :acc_dest, :amount, :transaction_type, :memo, :total)");
            $r1 = $stmt->execute([
                ":acc_src" => $acc_src_id,
                ":acc_dest" => $acc_dest_id,
                ":amount" => (-$transfer_amount),
                ":transaction_type" => $transaction_type,
                ":memo" => $memo,
                ":total" => $acc_src_newBalance
            ]);
            $stmt = $db->prepare("INSERT INTO Transactions(account_source, account_destination, balance_change, transaction_type, memo, expected_total) VALUES(:acc_src, :acc_dest, :amount, :transaction_type, :memo, :total)");
            $r2 = $stmt->execute([
                ":acc_src" => $acc_dest_id,
                ":acc_dest" => $acc_src_id,
                ":amount" => $transfer_amount,
                ":transaction_type" => $transaction_type,
                ":memo" => $memo,
                ":total" => $acc_dest_newBalance
            ]);

            if ($r1 && $r2) {
                $stmt = $db->prepare("UPDATE Accounts set balance = :balance where id = :acc_id");
                $t1 = $stmt->execute([":acc_id" => $acc_src_id, ":balance" => $acc_src_newBalance]);
                $stmt = $db->prepare("UPDATE Accounts set balance = :balance where id = :acc_id");
                $t2 = $stmt->execute([":acc_id" => $acc_dest_id, ":balance" => $acc_dest_newBalance]);
                if ($t1 && $t2) {
                    flash("Transfer Successful");
                }
            }
        }
    } else {
        flash("Cannot tranfer between the same account");
    }
}
?>

<?php require(__DIR__ . "/partials/flash.php"); ?>
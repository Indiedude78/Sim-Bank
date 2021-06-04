<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php if (!is_logged_in()) {
    die(header("Location: login.php"));
} ?>
<?php
require_once(__DIR__ . "/partials/dashboard.php");
?>
<?php
$user_id = get_id();
$src_acc_result = [];
$dest_acc_result = [];
if (isset($user_id)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT account_number FROM Accounts where user_id = :user_id and closed = 0 and frozen = 0 AND account_type != 'Loan'");
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

<div class="form-container">
    <h3>Send Funds</h3>
    <form id="ext-transfer-form" method="POST">
        <label for="src_acc_num">From:</label>
        <select id="src_acc_num" name="src_acc_num" placeholder="Source Account" required>
            <option value="" disabled selected>Choose Source Account</option>
            <?php
            if (isset($result)) {
                foreach ($result as $r) {
                    echo "<option value=" . $r["account_number"] . ">" . $r["account_number"] . "</option>";
                }
            }
            ?>
        </select>
        <label for="dest_acc_user">Search by Last Name:</label>
        <input type="text" id="dest_acc_user" name="dest_acc_user" placeholder="Last Name" required />
        <label for="dest_acc_num">Last 4 digits of Destination Account:</label>
        <input type="number" id="dest_acc_num" name="dest_acc_num" placeholder="xxxx" pattern=".{4}" minlength="4" required title="4 characters only" />
        <input type="submit" id="search" name="search" value="Search" />
    </form>
</div>

<?php
if (isset($_POST["search"])) {
    $src_acc_num = $_POST["src_acc_num"];
    $dest_user_lname = $_POST["dest_acc_user"];
    $dest_acc_partial_num = $_POST["dest_acc_num"];
    if (isset($src_acc_num) && isset($dest_user_lname) && strlen($dest_acc_partial_num) == 4) {
        $db = getDB();
        $stmt = $db->prepare("SELECT id, balance from Accounts WHERE user_id = :user_id and account_number = :acc_num");
        $r = $stmt->execute([
            ":user_id" => $user_id,
            ":acc_num" => $src_acc_num
        ]);
        $src_acc_result = $stmt->fetch(PDO::FETCH_ASSOC);
        //echo var_dump($src_acc_result);

        //$stmt = $db->prepare("SELECT id FROM Users WHERE lname = :lname");
        //$r = $stmt->execute([":lname" => $dest_user_lname]);
        //$result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt = $db->prepare("SELECT Users.lname, Users.fname, Users.id, Users.is_private, Users.email, account_number, Accounts.id, account_type, balance FROM Accounts JOIN Users ON Accounts.user_id = Users.id WHERE Users.lname = :last_name AND account_number LIKE :acc_num AND Accounts.id != '1' AND Users.disabled != '1' LIMIT 1");
        $r = $stmt->execute([
            ":last_name" => $dest_user_lname,
            ":acc_num" => "%$dest_acc_partial_num"
        ]);
        $dest_acc_result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($dest_acc_result)) {
            flash("No results found");
        }
    } else {
        flash("Please enter the last 4 digits of the account you wish to transfer to");
    }
}
?>

<div class="form-container">
    <?php if (isset($dest_acc_result) && $dest_acc_result) : ?>
        <h3>Confirmation</h3>
        <form id="searched-ext-transfer-form" method="POST">
            <label for="src_acc_prefilled">Source Account:</label>
            <input type="text" disabled readonly id="src_acc_prefilled" name="src_acc_prefilled" value="<?php safer_echo($src_acc_num); ?>" />
            <label for="src_acc_balance_prefilled">Account Balance:</label>
            <input type="number" disabled readonly id="src_acc_balance_prefilled" name="src_acc_balance_prefilled" value="<?php safer_echo($src_acc_result["balance"]); ?>" />
            <label for="dest_acc_prefilled">Destination Account:</label>
            <input type="text" disabled readonly id="dest_acc_prefilled" name="dest_acc_prefilled" value="<?php safer_echo($dest_acc_result["account_number"]); ?>" />
            <label for="dest_user_prefilled">Destination User:</label>
            <input type="text" disabled readonly id="dest_user_prefilled" name="dest_user_prefilled" value="<?php safer_echo($dest_acc_result["fname"] . " " . $dest_acc_result["lname"]); ?>" />
            <?php if ($dest_acc_result["is_private"] == 0) : ?>
                <label for="dest_user_prefilled_email">Destination User Email:</label>
                <input type="text" disabled readonly id="dest_user_prefilled_email" name="dest_user_prefilled_email" value="<?php safer_echo($dest_acc_result["email"]) ?>" />
            <?php endif; ?>
            <label for="amount">Transfer Amount:</label>
            <input type="number" id="amount" name="amount" placeholder="00.00" min="1" step="any" required />
            <label for="memo">Memo:</label>
            <textarea id="memo" name="memo" rows="4" cols="30" placeholder="Memo"></textarea>
            <input type="hidden" name="src_acc_id" value="<?php safer_echo($src_acc_result["id"]); ?>" />
            <input type="hidden" name="dest_acc_id" value="<?php safer_echo($dest_acc_result["id"]); ?>" />
            <input type="hidden" name="dest_acc_balance" value="<?php safer_echo($dest_acc_result["balance"]); ?>" />
            <input type="submit" id="submit" name="transfer" value="Transfer" />
        </form>
    <?php endif; ?>
</div>

<?php
if (isset($_POST["transfer"])) {
    $src_acc_id = $_POST["src_acc_id"];
    $dest_acc_id = $_POST["dest_acc_id"];
    $src_acc_balance = $_POST["src_acc_balance_prefilled"];
    $dest_acc_balance = $_POST["dest_acc_balance"];
    if ($src_acc_id == null || $dest_acc_id == null || $src_acc_balance == null || $dest_acc_balance == null) {
        flash("Missing critical information, transfer cancelled");
    } else {

        if ($_POST["amount"] > $src_acc_balance) {
            flash("Insufficient Funds");
        } else {
            if (isset($_POST["memo"])) {
                $memo = $_POST["memo"];
                //flash("memo recorded");
            } else {
                $memo = "";
            }


            $transfer_amount = $_POST["amount"];
            $transaction_type = "ext-transfer";
            $expected_total_src = $src_acc_balance - $transfer_amount;
            $stmt = $db->prepare("INSERT INTO Transactions(account_source, account_destination, balance_change, transaction_type, memo, expected_total) VALUES(:acc_src, :acc_dest, :balance_change, :t_type, :memo, :total)");
            $r = $stmt->execute([
                ":acc_src" => $src_acc_id,
                ":acc_dest" => $dest_acc_id,
                ":balance_change" => - ($transfer_amount),
                ":t_type" => $transaction_type,
                ":memo" => $memo,
                ":total" => $expected_total_src
            ]);
            if ($r) {
                $expected_total_dest = $transfer_amount + $dest_acc_balance;
                $stmt2 = $db->prepare("INSERT INTO Transactions(id, account_source, account_destination, balance_change, transaction_type, memo, expected_total) VALUES(:id, :acc_src, :acc_dest, :balance_change, :t_type, :memo, :total)");
                $r2 = $stmt2->execute([
                    ":id" => $db->lastInsertId() + 1,
                    ":acc_src" => $dest_acc_id,
                    ":acc_dest" => $src_acc_id,
                    ":balance_change" => $transfer_amount,
                    ":t_type" => $transaction_type,
                    ":memo" => $memo,
                    ":total" => $expected_total_dest
                ]);
                if ($r2) {
                    $stmt3 = $db->prepare("UPDATE Accounts set balance = :balance where id = :acc_id");
                    $r3 = $stmt3->execute([
                        ":balance" => $expected_total_src,
                        ":acc_id" => $src_acc_id
                    ]);
                    if ($r3) {
                        $stmt4 = $db->prepare("UPDATE Accounts set balance = :balance where id = :acc_id");
                        $r4 = $stmt4->execute([
                            ":balance" => $expected_total_dest,
                            ":acc_id" => $dest_acc_id
                        ]);
                    }
                }
            }

            if ($r && $r2 && $r3 && $r4) {
                flash("External transfer successful");
            } else {
                flash("Something went wrong");
            }
        }
    }
}

?>

<?php require(__DIR__ . "/partials/flash.php"); ?>

<script src="jquery/jquery.js"></script>
<script src="static/js/form_animation.js"></script>
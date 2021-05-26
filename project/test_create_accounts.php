<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
if (!is_logged_in()) {
    flash("You do not have permission to view this page");
    die(header("Location : login.php"));
}
?>
<?php
require_once(__DIR__ . "/partials/dashboard.php");
//Set savings account interest to 0.5% if less than $10,000 else 1%
$interest_rate = 5;
//Find APY Formula

?>
<div class="form-container">
    <h3>Open New Account</h3>
    <form id="create-account-form" method="POST">
        <label for="account_num">Account Number:</label>
        <input type="number" id="account_num" name="account_num" maxlength="12" value="<?php echo set_account_num(12); ?>" required readonly />
        <label for="account_type">Account Type:</label>
        <select id="account_type" name="account_type" placeholder="Account Type" required>
            <option disabled selected value="">Account Type</option>
            <option value="Checking">Checking</option>
            <option value="Saving">Saving</option>
        </select>
        <div class="hide">
            <label>Interest Rate: <?php echo $interest_rate . " %"; ?></label><br><br>
            <label for="compounding_time">Total Years:</label>
            <input type="number" id="compounding_time" name="compounding_time" min=0 step="1" placeholder="Number of years" />
        </div>
        <label for="balance">Balance:</label>
        <input type="number" id="balance" name="balance" min="0" step="any" placeholder="Deposit Amount" />
        <input type="submit" name="save" value="Create Account" />
    </form>
</div>
<?php

if (isset($_POST["save"])) {
    $account_num = $_POST["account_num"];
    $user_id = get_id();
    $account_type = $_POST["account_type"];
    $balance = $_POST["balance"];


    if ($balance > 5) {
        if ($account_type == "Saving") {
            $time_period = $_POST["compounding_time"];
            $current_interest_rate = $interest_rate / 100;
            $apy = pow((1 + ($current_interest_rate / $time_period)), $time_period) - 1;
            $future_value = $balance * pow((1 + $apy), $time_period);
        } else {
            $time_period = 0;
            $apy = 0;
        }
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Accounts(account_number, user_id, account_type, balance, apy, total_years) VALUES(:account_number, :user_id, :account_type, :balance, :apy, :total_years)");
        $r = $stmt->execute([
            ":account_number" => $account_num,
            ":user_id" => $user_id,
            ":account_type" => $account_type,
            ":balance" => $balance,
            ":apy" => $apy,
            ":total_years" => $time_period
        ]);
        if ($r) {
            flash("Account created successfully");
            flash("Account number: " . $account_num);
            if ($apy > 0 && $time_period > 0) {
                flash("APY: " . number_format($apy, 2));
                flash("Final value after $time_period years: $" . number_format($future_value, 2));
            }
            $account_src = 1;
            $account_dest = $db->lastInsertId();
            $balance = -$balance;
            $transaction_type = "deposit";
            $memo = "Initial deposit";
            $total = $balance;
            $stmt = $db->prepare("INSERT INTO Transactions(account_source, account_destination, balance_change, transaction_type, memo, expected_total) VALUES(:account_src, :account_dest, :balance_change, :transaction_type, :memo, :total)");
            $r = $stmt->execute([
                ":account_src" => $account_src,
                ":account_dest" => $account_dest,
                ":balance_change" => $balance,
                ":transaction_type" => $transaction_type,
                ":memo" => $memo,
                ":total" => $total
            ]);
            $stmt = $db->prepare("SELECT balance FROM Accounts where id = 1");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            //flash($result["balance"]);
            $newBalance = $total + $result["balance"];
            //flash($newBalance);
            $stmt = $db->prepare("UPDATE Accounts set balance = :balance where id = 1 and account_number = 000000000000");
            $r = $stmt->execute([":balance" => $newBalance]);




            if ($r) {
                flash("Transaction recorded");
                $account_src = $account_dest;
                $account_dest = 1;
                $balance = -$balance;
                $transaction_type = "deposit";
                $memo = "Initial deposit";
                $total = $balance;
                $stmt = $db->prepare("INSERT INTO Transactions(account_source, account_destination, balance_change, transaction_type, memo, expected_total) VALUES(:account_src, :account_dest, :balance_change, :transaction_type, :memo, :total)");
                $r = $stmt->execute([
                    ":account_src" => $account_src,
                    ":account_dest" => $account_dest,
                    ":balance_change" => $balance,
                    ":transaction_type" => $transaction_type,
                    ":memo" => $memo,
                    ":total" => $total
                ]);
            }
        } else {
            $e = $stmt->errorInfo();
            //echo var_dump($e);
            flash("Something went wrong, Please try again");
        }
    } else {
        flash("Minimum deposit amount: $5");
    }
}


?>


<?php require(__DIR__ . "/partials/flash.php"); ?>

<script src="jquery/jquery.js"></script>

<script>
    $(document).ready(function() {
        $(".hide").hide();
        var $accounts = $("#account_type");
        $accounts.change(function() {
            var currentType = $accounts.val();
            if (currentType == "Saving") {
                $(".hide").slideDown(200);
            } else {
                $(".hide").slideUp(200);
            }
        });
    });
</script>
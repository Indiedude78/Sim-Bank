<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
if (!is_logged_in()) {
    flash("You do not have permission to view this page");
    die(header("Location : login.php"));
}
?>
<?php
require_once(__DIR__ . "/partials/dashboard.php");
$user_id = get_id();
$interest_rate = 5;
?>

<?php
if (isset($_POST["submit"])) {
    if (isset($_POST["account_num"]) && isset($_POST["loan_amount"]) && isset($_POST["loan_period"])) {
        $loan_acc_num = $_POST["account_num"];
        $loan_amount = $_POST["loan_amount"];
        $loan_period = $_POST["loan_period"];
        $dest_account = $_POST["deposit_account"];
        if ($loan_amount >= 500) {

            $current_interest_rate = $interest_rate / 100;
            $total_periods = $loan_period * 12;
            $i = $current_interest_rate / 12;
            $payment_part1 = $loan_amount * $i * pow((1 + $i), $total_periods);
            $payment_part2 = pow((1 + $i), $total_periods) - 1;
            $total_payment_per_period =  $payment_part1 / $payment_part2;
            $total_payment = ($total_payment_per_period * $total_periods);
            $total_interest = $total_payment - $loan_amount;
            $db = getDB();
            $stmt = $db->prepare("SELECT id, account_number, account_type, balance FROM Accounts WHERE account_number = :acc_num and user_id = :user_id");
            $r = $stmt->execute([
                ":acc_num" => $dest_account,
                ":user_id" => $user_id
            ]);
            $result1 = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
}
?>

<div class="form-container">
    <?php if (isset($result1)) : ?>
        <h3>Loan Confirmation</h3>
        <form id="loan-confirmation-form" method="POST">
            <div id="user-account-confirm">
                <label for="account_number">Loan Deposit Account:</label>
                <input type="number" id="account_number" name="account_number" value="<?php echo $result1["account_number"]; ?>" readonly />
                <input type="hidden" name="account_id" value="<?php echo $result1["id"]; ?>" required />
                <label for="account_type">Account Type:</label>
                <input type="text" id="account_type" name="account_type" value="<?php echo $result1["account_type"] ?>" readonly />
                <label for="account_balance">Current Balance:</label>
                <input type="number" id="account_balance" name="account_balance" value="<?php echo $result1["balance"]; ?>" readonly />
            </div>
            <div id="loan-account-confirmation">
                <label for="prefilled_loan_acc_num">Loan Account Number:</label>
                <input type="number" id="prefilled_loan_acc_num" name="prefilled_loan_acc_num" value="<?php echo $loan_acc_num; ?>" readonly />
                <label for="prefilled_loan_amount">Total Loan Amount:</label>
                <input type="number" id="prefilled_loan_amount" name="prefilled_loan_amount" value="<?php echo $loan_amount; ?>" readonly required />
                <label for="prefilled_loan_period">Loan Period in Years:</label>
                <input type="number" id="prefilled_loan_period" name="prefilled_loan_period" value="<?php echo $loan_period ?>" readonly required />
            </div>
            <label for="prefilled_interest">Fixed Interest Rate: <?php echo number_format($interest_rate, 2); ?> %</label>
            <input type="hidden" name="interest_in_decimals" value="<?php echo $current_interest_rate; ?>" required readonly />
            <label for="prefilled_total_interest_amount_show">Total Interest:</label>
            <input id="prefilled_total_interest_amount_show" name="prefilled_total_interest_amount_show" value="<?php echo number_format($total_interest, 2); ?>" readonly />
            <input type="hidden" name="prefilled_total_interest_amount" value="<?php echo $total_interest; ?>" />
            <label for="prefilled_total_payment_show">Total Payment:</label>
            <input id="prefilled_total_payment_show" name="prefilled_total_payment_show" value="<?php echo number_format($total_payment, 2); ?>" readonly />
            <input type="hidden" name="prefilled_total_payment" value="<?php echo $total_payment; ?>" />
            <label for="prefilled_monthly_payment_show">Fixed Monthly Payments:</label>
            <input id="prefilled_monthly_payment_show" name="prefilled_monthly_payment_show" value="<?php echo number_format($total_payment_per_period, 2); ?>" readonly />
            <input type="hidden" name="prefilled_monthly_payment" value="<?php echo $total_payment_per_period; ?>" />
            <input type="submit" name="confirm" value="Confirm" />
        </form>
    <?php endif; ?>
</div>

<?php
if (isset($_POST["confirm"])) {
    $dest_acc_id = $_POST["account_id"];
    $dest_acc_balance = $_POST["account_balance"];
    $loan_acc_num = $_POST["prefilled_loan_acc_num"];
    $loan_amount = $_POST["prefilled_loan_amount"];
    $loan_period = $_POST["prefilled_loan_period"];
    $loan_interest = $_POST["interest_in_decimals"];
    $total_payment = $_POST["prefilled_total_payment"];
    $fixed_payments = $_POST["prefilled_monthly_payment"];
    $acc_type = "Loan";
    $db = getDB();
    $query = "SELECT balance FROM Accounts WHERE id = 1";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $world_account = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($world_account) {
        //echo "Ran 1";
        $world_balance = $world_account["balance"];
        $new_world_balance = $world_balance - $loan_amount;
        $stmt = $db->prepare("UPDATE Accounts set balance = :newBalance WHERE id = 1");
        $r = $stmt->execute([":newBalance" => $new_world_balance]);
        if ($r) {
            //echo "Ran 2";
            $new_dest_acc_balance = $dest_acc_balance + $loan_amount;
            $stmt = $db->prepare("UPDATE Accounts set balance = :newBalance WHERE id = :acc_id");
            $r1 = $stmt->execute([":newBalance" => $new_dest_acc_balance, ":acc_id" => $dest_acc_id]);
            if ($r1) {
                //echo "Ran 3";
                $query = "INSERT INTO Accounts(account_number, user_id, account_type, balance, apy, total_years, monthly_payment) ";
                $query .= "VALUES(:loan_acc, :user_id, :acc_type, :balance, :apy, :total_years, :payment)";
                $stmt = $db->prepare($query);
                $r2 = $stmt->execute([
                    ":loan_acc" => $loan_acc_num,
                    ":user_id" => get_id(),
                    ":acc_type" => $acc_type,
                    ":balance" => $total_payment,
                    ":apy" => $loan_interest,
                    ":total_years" => $loan_period,
                    ":payment" => $fixed_payments
                ]);
                $e = $stmt->errorInfo();
                //echo var_dump($e);
                if ($r2) {
                    //echo "Ran 4";
                    $memo = "LOAN-DEPOSIT";
                    $transaction_type = "loan-deposit";
                    $query = "INSERT INTO Transactions(account_source, account_destination, balance_change, transaction_type, memo, expected_total)";
                    $query .= " VALUES(:acc_src, :acc_dest, :balance_change, :t_type, :memo, :total)";
                    $stmt = $db->prepare($query);
                    $r3 = $stmt->execute([
                        ":acc_src" => 1,
                        ":acc_dest" => $dest_acc_id,
                        ":balance_change" => (-$loan_amount),
                        ":t_type" => $transaction_type,
                        ":memo" => $memo,
                        ":total" => $new_world_balance
                    ]);
                    if ($r3) {
                        //echo "Ran 5";
                        $query = "INSERT INTO Transactions(account_source, account_destination, balance_change, transaction_type, memo, expected_total)";
                        $query .= " VALUES(:acc_src, :acc_dest, :balance_change, :t_type, :memo, :total)";
                        $stmt = $db->prepare($query);
                        $r4 = $stmt->execute([
                            ":acc_src" => $dest_acc_id,
                            ":acc_dest" => 1,
                            ":balance_change" => $loan_amount,
                            ":t_type" => $transaction_type,
                            ":memo" => $memo,
                            ":total" => $new_dest_acc_balance
                        ]);

                        if ($r4) {
                            flash("Loan Application Successful");
                            flash("Monthly Payments set to: $" . number_format($fixed_payments, 2));
                        } else {
                            flash("Something went wrong");
                        }
                    }
                }
            }
        }
    } else {
        flash("Something went wrong");
    }
}

?>

<?php require(__DIR__ . "/partials/flash.php"); ?>

<script src="jquery/jquery.js"></script>
<script src="static/js/form_animation.js"></script>
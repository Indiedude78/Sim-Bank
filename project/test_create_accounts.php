<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
if (!has_role("Admin")) {
    flash("You do not have permission to view this page");
    die(header("Location : login.php"));
}
?>

<form id="user-reg" class="user-reg" method="POST">
    <label for="account_num">Account Number:</label>
    <input type="number" id="account_num" name="account_num" maxlength="12" value="<?php echo set_account_num(12);?>" required readonly/>
    <label for="account_type">Account Type:</label>
    <select id="account_type" name="account_type" placeholder="Account Type" required>
        <option value="Checking">Checking</option>
        <option value="Saving">Saving</option>
    </select>
    <label for="balance">Balance:</label>
    <input type="number" id="balance" name="balance" min="0" step="any" placeholder="Deposit Amount"/>
    <input type="submit" name="save" value="Create Account"/>
</form>

<?php

if (isset($_POST["save"])) {
    $account_num = $_POST["account_num"];
    $user_id = get_id();
    $account_type = $_POST["account_type"];
    $balance = $_POST["balance"];
    if ($balance > 5) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO Accounts(account_number, user_id, account_type, balance) VALUES(:account_number, :user_id, :account_type, :balance)");
        $r = $stmt->execute([
            ":account_number"=>$account_num,
            ":user_id"=>$user_id,
            ":account_type"=>$account_type,
            ":balance"=>$balance
        ]);
        if ($r) {
            flash("Account created successfully");
            flash("Account number: " . $account_num);
        }
        else {
            $e = $stmt->errorInfo();
            flash("Something went wrong, Please try again");
        }
    }
    else {
        flash("Minimum deposit amount: $5");
    }
}

?>
<?php require(__DIR__ . "/partials/flash.php"); ?>
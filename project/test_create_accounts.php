<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
if (!has_role("Admin")) {
    flash("You do not have permission to view this page");
    die(header("Location : login.php"));
}
?>

<form id="user-reg" class="user-reg" method="POST">
    <label for="account_num">Account Number:</label>
    <input type="number" id="account_num" name="account_num" maxlength="12" placeholder="Account Number" required/>
    <label for="account_type">Account Type:</label>
    <input type="text" id="account_type" name="account_type" maxlength="20" placeholder="Account Type" required/>
    <label for="balance">Balance:</label>
    <input type="number" id="balance" name="balance" placeholder="Balance"/>
    <input type="submit" name="save" value="Create"/>
</form>

<?php

if (isset($_POST["save"])) {
    $account_num = $_POST["account_num"];
    $user_id = get_id();
    $account_type = $_POST["account_type"];
    $balance = $_POST["balance"];  
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO Accounts(account_number, user_id, account_type, balance) VALUES(:account_number, :user_id, :account_type, :balance)");
    $r = $stmt->execute([
        ":account_number"=>$account_num,
        ":user_id"=>$user_id,
        ":account_type"=>$account_type,
        ":balance"=>$balance
    ]);
    if ($r) {
        flash("Created successfully with id: " . $db->lastInsertId());
    }
    else {
        $e = $stmt->errorInfo();
        flash("Error creating " . var_export($e, true));
    }
}

?>
<?php require(__DIR__ . "/partials/flash.php"); ?>
<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
if (!has_role("Admin")) {
    flash("You do not have permission to view this page");
    die(header("Location : login.php"));
}
?>

<?php 
if (isset($_GET["id"])) {
    $id = $_GET["id"];
}
?>

<?php

if (isset($_POST["save"])) {
    $account_num = $_POST["account_num"];
    $user_id = get_id();
    $account_type = $_POST["account_type"];
    $balance = $_POST["balance"];  
    $db = getDB();
    if (isset($id)) {
        $stmt = $db->prepare("UPDATE Accounts set account_number=:account_num, account_type=:account_type, balance=:balance where id=:id");
        $r = $stmt->execute([
            ":account_num"=>$account_num,
            ":account_type"=>$account_type,
            ":balance"=>$balance,
            ":id"=>$id
    ]);
        if ($r) {
            flash("Updated successfully with id: " . $id);
        }
        else {
            $e = $stmt->errorInfo();
            flash("Error creating " . var_export($e, true));
        }
    }
    else {
        flash("Invalid ID or ID not set");
    }
}

?>

<?php 
$result = [];
if (isset($id)) {
    $id = $_GET["id"];
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM Accounts where id=:id");
    $r = $stmt->execute([":id"=>$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC); 
}
?>

<form id="user-reg" class="user-reg" method="POST">
    <label for="account_num">Account Number:</label>
    <input type="number" id="account_num" name="account_num" maxlength="12" placeholder="Account Number" value="<?php safer_echo($result["account_number"]);?>" required/>
    <label for="account_type">Account Type:</label>
    <input type="text" id="account_type" name="account_type" maxlength="20" placeholder="Account Type" value="<?php safer_echo($result["account_type"]);?>" required/>
    <label for="balance">Balance:</label>
    <input type="number" id="balance" name="balance" value="<?php safer_echo($result["balance"]); ?>" placeholder="Balance"/>
    <input type="submit" name="save" value="Update"/>
</form>
<?php require(__DIR__ . "/partials/flash.php"); ?>
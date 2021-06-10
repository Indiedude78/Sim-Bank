<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php if (!is_logged_in()) {
    die(header("Location: login.php"));
} ?>
<?php
if (!has_role("Admin")) {
    flash("You do not have permission to view this page");
    die(header("Location : home.php"));
}
?>
<?php
require_once(__DIR__ . "/partials/dashboard.php");
?>

<?php
if (isset($_GET["id"])) {
    $user_id = $_GET["id"];

    $db = getDB();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    $stmt = $db->prepare("SELECT Users.email, Users.fname, Users.lname, Users.disabled FROM Users WHERE Users.id = :user_id");
    $r = $stmt->execute([
        ":user_id" => $user_id,
    ]);
    $user_result = $stmt->fetch(PDO::FETCH_ASSOC);
    //echo var_dump($result);

    if (isset($user_result)) {
        $first_name = $user_result["fname"];
        $last_name = $user_result["lname"];
        $email = $user_result["email"];
        $isDisabled = $user_result["disabled"];
        if ($isDisabled == 1) {
            $isDisabled = "Yes";
        } else {
            $isDisabled = "No";
        }
    }

    $stmt = $db->prepare("SELECT Accounts.id, Accounts.account_number, Accounts.user_id, Accounts.account_type, Accounts.balance, Accounts.apy, Accounts.total_years, Accounts.monthly_payment, Accounts.closed, Accounts.frozen FROM Accounts JOIN Users ON Users.id = Accounts.user_id WHERE Users.id = :user_id");
    $r = $stmt->execute([":user_id" => $user_id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!isset($result)) {
        flash("No Accounts Found");
    }
}
?>

<?php if (isset($result)) : ?>
    <div class="search-results">
        <div class="user-info-container">
            <h1>User Information</h1>
            <div class="user-info">
                <h2><span class="info-label">Name:</span> <span class="info-items"><?php safer_echo($first_name . " " . $last_name); ?></span></h2>
                <h2><span class="info-label">Email:</span> <span class="info-items"><?php safer_echo($email); ?></span></h2>
                <h2><span class="info-label">Disabled</span><span class="info-item"><?php safer_echo($isDisabled); ?></span></h2>
            </div>
            <?php foreach ($result as $r) : ?>
                <div class="user-account-info" id="admin-search">
                    <h3><span class="info-label">Account Number:</span> <span class="info-items"><?php safer_echo($r["account_number"]); ?></span></h3>
                    <h3><span class="info-label">Account Type:</span> <span class="info-items"><?php safer_echo($r["account_type"]); ?></span></h3>
                    <?php
                    if ($r["closed"] == 1) {
                        $close_restriction = "Closed";
                    } else {
                        $close_restriction = "None";
                    }
                    if ($r["frozen"] == 1) {
                        $freeze_restriction = "Frozen";
                    } else {
                        $freeze_restriction = "None";
                    }
                    ?>
                    <h3><span class="info-label">Restrictions:</span> <span class="info-items"><?php safer_echo($freeze_restriction . "-" . $close_restriction) ?></span></h3>
                    <?php if ($r["account_type"] != "Checking") : ?>
                        <h3><span class="info-label">APY:</span> <span class="info-items"><?php safer_echo($r["apy"]); ?> %</span></h3>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="button-container">
            <form class="buttons" method="POST">
                <input type="submit" id="freeze" name="freeze" value="Freeze Account" />
                <input type="hidden" name="acc_id" value="<?php echo $acc_id; ?>" />
                <input type="submit" id="disable" name="disable" value="Disable User" />
                <input type="hidden" name="user_id" value="<?php echo $searched_user_id; ?>" />
            </form>
        </div>
    </div>
<?php endif; ?>

<?php
if (isset($_POST["freeze"])) {
    $freeze_acc_id = $_POST["acc_id"];
    $db = getDB();
    $stmt = $db->prepare("UPDATE Accounts set frozen = 1 WHERE id = :acc_id");
    $r = $stmt->execute([":acc_id" => $freeze_acc_id]);
    if ($r) {
        flash("Account Freeze Successful");
    } else {
        flash("Account Freeze Error");
    }
}

if (isset($_POST["disable"])) {
    $disable_user_id = $_POST["user_id"];
    $db = getDB();
    $stmt = $db->prepare("UPDATE Users set `disabled` = 1 WHERE id = :user_id");
    $r = $stmt->execute([":user_id" => $disable_user_id]);
    if ($r) {
        flash("User Account disabled successfully");
    } else {
        flash("Unexpected Error");
    }
}
?>

<?php require(__DIR__ . "/partials/flash.php"); ?>
<script src="jquery/jquery.js"></script>

<script>
    $(document).ready(function() {

        var $freezeButton = $(".button-container");
        var isFrozen = <?php echo $result[0]["frozen"]; ?>;
        //console.log(isFrozen);
        if (isFrozen == 1) {
            //console.log(isFrozen);
            $freezeButton.find("#freeze").hide();
        }

        var isDisabled = <?php echo $user_result["disabled"]; ?>;
        if (isDisabled == 1) {
            $freezeButton.find("#disable").hide();
        }
    });
</script>

<?php
/* SELECT Users.email, Users.fname, Users.lname, Users.disabled, Accounts.id, 
Accounts.account_number, Accounts.user_id, Accounts.account_type, Accounts.balance,
 Accounts.apy, Accounts.total_years, Accounts.monthly_payment, Accounts.closed, 
 Accounts.frozen FROM Accounts JOIN Users ON Users.id = Accounts.user_id
  WHERE Users.id = :user_id" */
?>
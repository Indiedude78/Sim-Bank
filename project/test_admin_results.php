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
if (isset($_POST["search"])) {
    $fname = $_POST["first_name"];
    $lname = $_POST["last_name"];
    $acc_num = $_POST["acc_num"];
    $db = getDB();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    $stmt = $db->prepare("SELECT Users.email, Users.fname, Users.lname, Users.disabled, Accounts.id, Accounts.account_number, Accounts.user_id, Accounts.account_type, Accounts.balance, Accounts.apy, Accounts.total_years, Accounts.monthly_payment, Accounts.closed, Accounts.frozen FROM Accounts JOIN Users ON Users.id = Accounts.user_id WHERE Users.fname = :fname AND Users.lname = :lname AND Accounts.account_number = :acc_num");
    $r = $stmt->execute([
        ":fname" => $fname,
        ":lname" => $lname,
        ":acc_num" => $acc_num
    ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    //echo var_dump($result);

    if (isset($result) && $result) {
        $acc_id = $result["id"];
        $acc_balance = $result["balance"];
        $acc_num = $result["account_number"];
        $acc_type = $result["account_type"];
        if ($acc_type != "Checking") {
            $acc_apy = $result["apy"];
            $acc_time = $result["total_years"];
            if ($acc_type == "Loan") {
                $acc_payment = $result["monthly_payment"];
            }
        }
        $acc_closed_status = $result["closed"];
        $acc_freeze_status = $result["frozen"];
        $close_restriction = "NONE";
        if ($acc_closed_status == 1) {
            $close_restriction = "CLOSED";
        }
        $freeze_restriction = "NONE";
        if ($acc_freeze_status == 1) {
            $freeze_restriction = "FROZEN";
        }
        $searched_user_id = $result["user_id"];
        $searched_user_name = $result["fname"] . " " . $result["lname"];
        $searched_user_email = $result["email"];
        $searched_user_disabled = $result["disabled"];

        $stmt = $db->prepare("SELECT COUNT(*) as numOfTransactions FROM Transactions WHERE account_source = :acc_src");
        $r = $stmt->execute([":acc_src" => $acc_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $numOfRecords = $result["numOfTransactions"];
        if (isset($_GET["page"]) && $_GET["page"] > 0) {
            $page = $_GET["page"];
        } else {
            $page = 1;
        }
        $perPage = 10;

        $total_pages = ceil($numOfRecords / $perPage);
        $offset = ($page - 1) * $perPage;

        $stmt = $db->prepare("SELECT balance_change, transaction_type, memo, DATE(transaction_time) as transaction_time FROM Transactions WHERE account_source = :acc_src AND account_source != 1 ORDER BY transaction_time DESC LIMIT :offset, :per_page");
        $r1 = $stmt->execute([":acc_src" => $acc_id, ":offset" => $offset, ":per_page" => $perPage]);
        $result1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //echo var_dump($result1);
    } else {
        flash("No match found");
    }
}
?>

<?php if (isset($result) && isset($result1)) : ?>
    <div id="search-results">
        <div id="user-info-container">
            <h1>User Information</h1>
            <div id="user-info">
                <h2><span class="info-label">Name:</span> <span class="info-items"><?php safer_echo($searched_user_name); ?></span></h2>
                <h2><span class="info-label">Email:</span> <span class="info-items"><?php safer_echo($searched_user_email); ?></span></h2>
            </div>
            <div id="user-account-info">
                <h3><span class="info-label">Account Number:</span> <span class="info-items"><?php safer_echo($acc_num); ?></span></h3>
                <h3><span class="info-label">Account Type:</span> <span class="info-items"><?php safer_echo($acc_type); ?></span></h3>
                <h3><span class="info-label">Restrictions:</span> <span class="info-items"><?php safer_echo($freeze_restriction . "-" . $close_restriction) ?></span></h3>
                <?php if ($acc_type != "Checking") : ?>
                    <h3><span class="info-label">APY:</span> <span class="info-items"><?php safer_echo($acc_apy); ?> %</span></h3>
                <?php endif; ?>
            </div>
        </div>
        <div class="transaction-table">
            <table>
                <h2>Recent Transactions</h2>
                <thead>
                    <tr>
                        <th>Balance Change</th>
                        <th>Transaction Type</th>
                        <th>Memo</th>
                        <th>Transaction Time</th>
                    </tr>
                </thead>
                <?php if (isset($result1)) : ?>
                    <?php foreach ($result1 as $r) : ?>
                        <tbody>
                            <tr>
                                <td class="data-row"><?php safer_echo($r["balance_change"]); ?></td>
                                <td class="data-row"><?php safer_echo($r["transaction_type"]); ?></td>
                                <td class="data-row"><?php safer_echo($r["memo"]); ?></td>
                                <td class="data-row"><?php safer_echo($r["transaction_time"]); ?></td>
                            </tr>
                        </tbody>
                    <?php endforeach; ?>
                    <tfoot>
                        <tr>
                            <td colspan="4" id="total"><?php safer_echo("$ " . $acc_balance) ?></td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
        <div class="button-container">
            <form id="buttons" method="POST">
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

        var $freezeButton = $("#buttons");
        var isFrozen = <?php echo $acc_freeze_status; ?>;
        //console.log(isFrozen);
        if (isFrozen == 1) {
            //console.log(isFrozen);
            $freezeButton.find("#freeze").hide();
        }

        var isDisabled = <?php echo $searched_user_disabled; ?>;
        if (isDisabled == 1) {
            $freezeButton.find("#disable").hide();
        }
    });
</script>
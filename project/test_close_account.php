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
    $stmt = $db->prepare("SELECT account_number, account_type FROM Accounts where user_id = :user_id AND closed != 1 AND account_type != 'Loan'");
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
    <h3>Close Account</h3>
    <form id="close-account-form" method="POST">
        <label for="account_number">Choose Account</label>
        <select id="account_number" name="account_number" required>
            <option disabled selected value="">Choose Account to Close</option>
            <?php
            if (isset($result)) {
                foreach ($result as $r) {
                    if ($r["account_type"] != "Loan") {
                        echo "<option value=" . $r["account_number"] . ">" . $r["account_number"] . "</option>";
                    }
                }
            }
            ?>
        </select>
        <input type="submit" id="submit" name="search" value="Close" />
    </form>
</div>

<?php
if (isset($_POST["search"]) && isset($_POST["account_number"])) {
    $acc_num = $_POST["account_number"];
    $db = getDB();
    $stmt = $db->prepare("SELECT id, balance FROM Accounts WHERE account_number = :acc_num");
    $r = $stmt->execute([":acc_num" => $acc_num]);
    if ($r) {
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    $acc_id = $result["id"];
    $acc_balance = $result["balance"];
    if ($acc_balance > 0) {
        flash("You must empty all funds from the account to close it");
    } else {
        $stmt = $db->prepare("UPDATE Accounts set closed = 1 WHERE id = :acc_id");
        $r1 = $stmt->execute([":acc_id" => $acc_id]);
        if ($r1) {
            flash("Account closed successfully");
        } else {
            flash("Problem closing account");
        }
    }
}
?>


<?php require(__DIR__ . "/partials/flash.php"); ?>

<script src="jquery/jquery.js"></script>
<script src="static/js/form_animation.js"></script>
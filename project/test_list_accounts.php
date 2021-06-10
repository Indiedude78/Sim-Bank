<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    flash("You do not have permission to view this page");
    die(header("Location: home.php"));
}
?>
<?php
require_once(__DIR__ . "/partials/dashboard.php");
?>

<?php

$user_id = get_id();
$db = getDB();
$query = "SELECT id, account_number, account_type, balance, apy, total_years, monthly_payment, closed, frozen FROM Accounts ";
$query .= "WHERE user_id = :user_id AND id != 1 AND account_type != 'World' AND closed != 1 AND account_type != 'Loan'";
$stmt = $db->prepare($query);
$r = $stmt->execute([":user_id" => $user_id]);
$e = $stmt->errorInfo();

if ($e[0] != "00000") {
    flash("Something went wrong");
}

$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php if (isset($result)) : ?>
    <div class="search-results">
        <div class="user-info-container">
            <h1>Accounts</h1>
            <div class="user-account-info">
                <?php foreach ($result as $r) : ?>
                    <a href="test_transaction_history.php?id=<?php safer_echo($r["id"]); ?>">
                        <h3>
                            <span class="info-label">Account Number:</span>
                            <span class="info-items"><?php safer_echo($r["account_number"]); ?></span>
                        </h3>
                        <h3>
                            <span class="info-label">Account Type:</span>
                            <span class="info-items"><?php safer_echo($r["account_type"]); ?></span>
                        </h3>
                        <h3>
                            <span class="info-label">Balance:</span>
                            <span class="info-items">$ <?php safer_echo($r["balance"]); ?></span>
                        </h3>
                    </a>
                <?php endforeach; ?>
            </div>

        </div>
    </div>
<?php endif; ?>

<?php require(__DIR__ . "/partials/flash.php"); ?>

<script src="jquery/jquery.js"></script>
<script src="static/js/form_animation.js"></script>
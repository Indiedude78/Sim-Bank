<?php require_once(__DIR__ . "/partials/nav.php"); ?>

<?php
if (!has_role("Admin")) {
    flash("You do not have permission to view this page");
    die(header("Location : login.php"));
}
?>
<?php
require_once(__DIR__ . "/partials/dashboard.php");
$interest_rate = 5;
$user_id = get_id();
$result = [];
if (isset($user_id)) {
    $db = getDB();
    $stmt = $db->prepare("SELECT account_number FROM Accounts WHERE user_id = :user_id AND account_type != 'Loan'");
    $r = $stmt->execute([":user_id" => $user_id]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<form action="test_loan_confirmation.php" id="user-reg" class="user-reg" method="POST">
    <label for="account_num">Loan Account Number:</label>
    <input type="number" id="account_num" name="account_num" maxlength="12" value="<?php echo set_account_num(12); ?>" required readonly />
    <label for="loan_amount">Loan Amount:</label>
    <input type="number" id="loan_amount" name="loan_amount" placeholder="Enter loan amount" min="500" step="50" required />
    <label>Interest Rate:</label>
    <h5><?php echo $interest_rate; ?> %</h5>
    <label for="loan_period">Loan Period in Years:</label>
    <input type="number" id="loan_period" name="loan_period" placeholder="Enter loan period" min="1" step="1" required />
    <label for="deposit_account">Choose Account to deposit loan:</label>
    <?php if (isset($result)) : ?>
        <select id="deposit_account" name="deposit_account" required>
            <option disabled selected value="">Choose deposit account</option>
            <?php foreach ($result as $r) : ?>
                <option value="<?php echo $r["account_number"] ?>"><?php echo $r["account_number"] ?></option>
            <?php endforeach; ?>
        </select>
    <?php endif; ?>
    <input type="submit" id="submit" name="submit" value="Submit" />
</form>
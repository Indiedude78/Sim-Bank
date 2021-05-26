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
$acc_type = array("Checking", "Saving");
?>
<div class="form-container">
    <h3>List Accounts</h3>
    <form id="search-account-form" method="GET">
        <label for="account_number">Search Accounts</label>
        <input type="number" id="account_number" name="account_number" placeholder="Account Number" min="0" minlength="4" maxlength="12" />
        <label for="account_type">View Accounts</label>
        <select id="account_type" name="account_type" placeholder="Account Type">
            <option value="None">Choose Account Type</option>
            <?php
            foreach ($acc_type as $acc) {
                echo "<option value='$acc'>$acc</option>";
            }
            ?>
        </select>
        <input type="submit" name="search" value="Search" />
    </form>
</div>
<?php
if (isset($_GET["search"])) {
    $user_id = get_id();
    $db = getDB();
    if (isset($_GET["account_type"]) && !isset($_GET["account_number"])) {
        $query_acc = $_GET["account_type"];
        $stmt = $db->prepare("SELECT * FROM Accounts WHERE user_id = :id and account_type = :acc_type and closed = 0 AND account_type != 'Loan'");
        $r = $stmt->execute([
            ":acc_type" => $query_acc,
            ":id" => $user_id
        ]);
    } elseif (isset($_GET["account_number"]) && ($_GET["account_type"]) == "None") {
        $query_number = $_GET["account_number"];
        $stmt = $db->prepare("SELECT * FROM Accounts WHERE user_id = :id and account_number like :acc_num and closed = 0 AND account_type != 'Loan'");
        $r = $stmt->execute([
            ":acc_num" => "%$query_number%",
            ":id" => $user_id
        ]);
    } elseif (isset($_GET["account_number"]) && isset($_GET["account_type"])) {
        $query_acc = $_GET["account_type"];
        $query_number = $_GET["account_number"];
        $stmt = $db->prepare("SELECT * FROM Accounts WHERE user_id = :id and account_number like :acc_num and account_type = :acc_type and closed = 0 AND account_type != 'Loan'");
        $r = $stmt->execute([
            ":acc_num" => "%$query_number%",
            ":acc_type" => $query_acc,
            ":id" => $user_id
        ]);
    } else {
        flash("No search criteria entered");
    }

    $e = $stmt->errorInfo();
    if ($e[0] != "00000") {
        flash("Something went wrong");
    }
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div id="account-result-container">
    <?php if (isset($result)) : ?>
        <table id="account-table">
            <thead>
                <tr>
                    <th>Account Type</th>
                    <th>Account Number</th>
                    <th>Available Balance</th>
                    <th>Detailed View</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $r) : ?>
                    <tr>
                        <td><?php echo $r["account_type"]; ?></td>
                        <td><?php echo $r["account_number"]; ?></td>
                        <td><?php echo $r["balance"]; ?></td>
                        <td><button onclick="location.href='test_transaction_history.php?id=<?php safer_echo($r['id']); ?>'">View</button></td>
                    </tr>
            </tbody>
        <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

<?php require(__DIR__ . "/partials/flash.php"); ?>
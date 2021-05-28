<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php if (!is_logged_in()) {
    die(header("Location: login.php"));
} ?>
<?php
require_once(__DIR__ . "/partials/dashboard.php");
?>

<?php
$transaction_types = array("deposit", "withdraw", "transfer", "ext-transfer", "loan-deposit");
?>

<div class="form-container">
    <h3>Filter Transactions</h3>
    <form id="filter-form" method="POST">
        <label for="transaction_type">Transaction Type:</label>
        <select id="transaction_type" name="transaction_type">
            <option value="" disabled selected>Choose Transaction Type</option>
            <?php
            foreach ($transaction_types as $t) {
                echo "<option value=" . $t . ">" . $t . "</option>";
            }
            ?>
        </select>
        <label for="start_date">Start Date:</label>
        <input type="date" id="start_date" name="start_date" placeholder="Start Date" />
        <label for="end_date">End Date:</label>
        <input type="date" id="end_date" name="end_date" placeholder="End Date" />
        <input type="submit" name="submit" value="Filter" />
        <input type="submit" name="reset" value="Show All" />
    </form>
</div>

<?php
if (isset($_GET["id"]) && $_GET["id"] != 1) {
    $acc_id = $_GET["id"];
    $user_id = get_id();
    $db = getDB();
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
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

    if (!isset($_POST["submit"])) {
        $stmt = $db->prepare("SELECT Accounts.account_number, Accounts.account_type, Accounts.apy, balance_change, transaction_type, memo, transaction_time, expected_total, Accounts.balance FROM Transactions JOIN Accounts ON Transactions.account_source = Accounts.id WHERE Accounts.id = :id and Accounts.user_id = :user_id ORDER BY Transactions.id DESC LIMIT :offset, :per_page");
        $r = $stmt->execute([
            ":id" => $acc_id,
            ":user_id" => $user_id,
            ":offset" => $offset,
            ":per_page" => $perPage
        ]);
        $e = $stmt->errorInfo();
        if ($e[0] != "00000") {
            flash("Something went wrong");
        }
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //echo var_dump($result);
        /*foreach ($result as $r) {
            echo substr($r["transaction_time"], 0, 10) . "<br>";
        }*/
    } elseif (isset($_POST["submit"])) {
        if (isset($_POST["transaction_type"]) && $_POST["start_date"] == null) {
            //echo "This is being run!!";
            $transaction_type = $_POST["transaction_type"];
            $stmt = $db->prepare("SELECT Accounts.account_number, Accounts.account_type, Accounts.apy, balance_change, transaction_type, memo, transaction_time, expected_total, Accounts.balance FROM Transactions JOIN Accounts ON Transactions.account_source = Accounts.id WHERE Accounts.id = :id and Accounts.user_id = :user_id and transaction_type = :transaction_type ORDER BY Transactions.id DESC LIMIT :offset, :per_page");
            $r = $stmt->execute([
                ":id" => $acc_id,
                ":user_id" => $user_id,
                ":transaction_type" => $transaction_type,
                ":offset" => $offset,
                ":per_page" => $perPage
            ]);
            $e = $stmt->errorInfo();
            if ($e[0] != "00000") {
                flash("Something went wrong");
            }
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } elseif (isset($_POST["transaction_type"]) && isset($_POST["start_date"])) {
            //echo "The other condition is being run!!";
            $transaction_type = $_POST["transaction_type"];
            $start_date = $_POST["start_date"];
            if (isset($_POST["end_date"])) {
                $end_date = $_POST["end_date"];
            } else {
                $end_date = get_todays_date('America/New_York');
            }
            // echo $start_date . "<br>" . $end_date;
            $stmt = $db->prepare("SELECT Accounts.account_number, Accounts.account_type, Accounts.apy, balance_change, transaction_type, memo, transaction_time, expected_total, Accounts.balance FROM Transactions JOIN Accounts ON Transactions.account_source = Accounts.id WHERE Accounts.id = :id and Accounts.user_id = :user_id and transaction_type = :transaction_type and transaction_time BETWEEN :startDate and :endDate ORDER BY Transactions.id DESC LIMIT :offset, :per_page");
            $r = $stmt->execute([
                ":id" => $acc_id,
                ":user_id" => $user_id,
                ":transaction_type" => $transaction_type,
                ":startDate" => $start_date,
                ":endDate" => $end_date,
                ":offset" => $offset,
                ":per_page" => $perPage
            ]);
            $e = $stmt->errorInfo();
            if ($e[0] != "00000") {
                flash("Something went wrong");
            }
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $stmt = $db->prepare("SELECT Accounts.account_number, Accounts.account_type, Accounts.apy, balance_change, transaction_type, memo, transaction_time, expected_total, Accounts.balance FROM Transactions JOIN Accounts ON Transactions.account_source = Accounts.id WHERE Accounts.id = :id and Accounts.user_id = :user_id ORDER BY Transactions.id DESC LIMIT :offset, :per_page");
            $r = $stmt->execute([
                ":id" => $acc_id,
                ":user_id" => $user_id,
                ":offset" => $offset,
                ":per_page" => $perPage
            ]);
            $e = $stmt->errorInfo();
            if ($e[0] != "00000") {
                flash("Something went wrong");
            }
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            //echo var_dump($result);
        }
    }
} else {
    flash("You do not have permission to do this!");
}
?>

<div class="transaction-information">
    <?php if (isset($result) && isset($result[0]["account_number"])) : ?>
        <div id="account-information">
            <h3>Account Number: <?php safer_echo($result[0]["account_number"]) ?></h3>
            <h4>Account Type: <?php safer_echo($result[0]["account_type"]) ?></h4>
            <?php if ($result[0]["account_type"] == "Saving") : ?>
                <h4>APY: <?php safer_echo($result[0]["apy"]); ?> %</h4>
            <?php endif; ?>
        </div>
        <div id="transaction-table">
            <table>
                <thead>
                    <tr>
                        <th>Balance Change</th>
                        <th>Transaction Type</th>
                        <th>Memo</th>
                    </tr>
                </thead>
                <?php if (isset($result)) : ?>
                    <?php foreach ($result as $r) : ?>
                        <tbody>
                            <tr>
                                <td class="data-row"><?php safer_echo($r["balance_change"]); ?></td>
                                <td class="data-row"><?php safer_echo($r["transaction_type"]); ?></td>
                                <td class="data-row"><?php safer_echo($r["memo"]); ?></td>
                            </tr>
                        </tbody>
                    <?php endforeach; ?>
                    <tfoot>
                        <tr>
                            <td colspan="3" id="total"><?php safer_echo("$ " . $result[0]["balance"]) ?></td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
        <div class="pagination-div">
            <nav class="page-navigation">
                <ul class="pagination">
                    <li>
                        <a href="<?php echo $_SERVER['PHP_SELF'] . "?id=" . $acc_id . "&page=" . ($page - 1) ?>" id="previous"><span class="material-icons">
                                navigate_before
                            </span>Previous</a>
                    </li>
                    <li>
                        <a href="<?php echo $_SERVER['PHP_SELF'] . "?id=" . $acc_id . "&page=" . ($page + 1) ?>" id="next">Next<span class="material-icons">
                                navigate_next
                            </span></a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>



<?php if (!isset($result[0]["account_number"])) : ?>
    <div id="invalid-result">
        <?php flash("No results found"); ?>
    </div>
<?php endif; ?>

<?php require(__DIR__ . "/partials/flash.php"); ?>


<script src="jquery/jquery.js"></script>
<script src="static/js/form_animation.js"></script>

<script>
    $(document).ready(function() {
        var currentPage = <?php echo $page; ?>;
        var maxPage = <?php echo $total_pages; ?>;
        if (currentPage == 1) {
            $("#previous").hide();
        }
        if (currentPage >= maxPage) {
            $("#next").hide();
        }
    });
</script>
<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
?>
<?php require_once(__DIR__ . "/../lib/helpers.php"); ?>

<?php
$db = getDB();
$query = "SELECT id, email, username, fname, lname, `disabled` FROM Users WHERE id != 1 and id != 2";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
$query = "SELECT Accounts.id, Accounts.account_number,";
$query .= "Accounts.user_id, Accounts.account_type, Accounts.balance, Accounts.apy, Accounts.closed, Accounts.frozen FROM Accounts ";
$query .= "WHERE Accounts.user_id != 1 AND Accounts.id != 1";
$stmt = $db->prepare($query);
$stmt->execute();
$result1 = $stmt->fetchAll(PDO::FETCH_ASSOC);
$data["users"] = $result;
$data["accounts"] = $result1;
if ($result && $result1) {
    echo json_encode($data, JSON_PRETTY_PRINT);
} else {
    echo "Error";
}

?>
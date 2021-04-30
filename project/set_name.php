<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php if (!is_logged_in()) {
    die(header("Location: login.php"));
} ?>
<?php flash("Please enter your name below"); ?>

<form id="user-reg" class="user-reg" method="POST">
    <label for="fname">First Name:</label>
    <input type="text" id="fname" name="fname" placeholder="Enter First Name" maxlength="60" required />
    <label for="lname">Last Name:</label>
    <input type="text" id="lname" name="lname" placeholder="Enter Last Name" maxlength="60" required />
    <input type="submit" id="submit" name="submit" value="Submit" />
</form>

<?php
if (isset($_POST["submit"])) {
    //echo "1st if";
    if (isset($_POST["fname"]) && isset($_POST["lname"]) && isset($_SESSION["user"])) {
        //echo "2nd if";
        $fname = $_POST["fname"];
        $lname = $_POST["lname"];
        $email = get_email();
        $db = getDB();
        $stmt = $db->prepare("UPDATE Users set fname = :fname, lname = :lname WHERE email = :email");
        $r = $stmt->execute([
            ":fname" => $fname,
            ":lname" => $lname,
            ":email" => $email
        ]);
        $e = $stmt->errorInfo();
        if ($e[0] == "00000") {
            flash("Profile Updated!");
        } else {
            flash("Something went wrong");
        }

        $user_id = get_id();
        $stmt = $db->prepare("SELECT fname, lname FROM Users WHERE id = :user_id");
        $r = $stmt->execute([":user_id" => $user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (isset($result)) {
            $_SESSION["user"]["fname"] = $result["fname"];
            $_SESSION["user"]["lname"] = $result["lname"];
        }
    }
}
?>

<?php require(__DIR__ . "/partials/flash.php"); ?>
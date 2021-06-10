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
$user_id = get_id();
?>

<div class="form-container">
    <h3>Search Users</h3>
    <form id="admin-search-form" method="POST">
        <label for="first_name">First Name</label>
        <input type="text" id="first_name" name="first_name" placeholder="Enter first name" required />
        <label for="last_name">Last Name</label>
        <input type="text" id="last_name" name="last_name" placeholder="Enter last name" required />
        <input type="submit" name="search" value="Search" />
    </form>
</div>

<?php
if (isset($_POST["search"])) {
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $db = getDB();
    $query = "SELECT Users.id, Users.email, Users.fname, Users.lname, Users.username FROM Users WHERE Users.fname = :fname AND Users.lname = :lname";
    $stmt = $db->prepare($query);
    $r = $stmt->execute([
        ":fname" => $first_name,
        ":lname" => $last_name
    ]);

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($result) {
        flash(sizeof($result) . " result(s) found");
    } else {
        flash("No Users Found");
    }
}
?>

<?php if (isset($result)) : ?>
    <div class="search-results">
        <div class="user-info-container">
            <h1>Users</h1>
            <div class="user-account-info">
                <?php foreach ($result as $r) : ?>
                    <a href="test_admin_results.php?id=<?php safer_echo($r["id"]); ?>">
                        <h3>
                            <span class="info-label">Name</span>
                            <span class="info-items"><?php safer_echo($r["fname"] . " " . $r["lname"]); ?></span>
                        </h3>
                        <h3>
                            <span class="info-label">Email</span>
                            <span class="info-items"><?php safer_echo($r["email"]); ?></span>
                        </h3>
                        <h3>
                            <span class="info-label">Username</span>
                            <span class="info-items"><?php safer_echo($r["username"]); ?></span>
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
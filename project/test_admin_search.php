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
    <form action="test_admin_results.php" id="admin-search-form" method="POST">
        <label for="first_name">First Name</label>
        <input type="text" id="first_name" name="first_name" placeholder="Enter first name" required />
        <label for="last_name">Last Name</label>
        <input type="text" id="last_name" name="last_name" placeholder="Enter last name" required />
        <label for="acc_num">Account Number</label>
        <input type="number" id="acc_num" name="acc_num" minlength="12" placeholder="Enter account number" required />
        <input type="submit" name="search" value="Search" />
    </form>
</div>


<?php require(__DIR__ . "/partials/flash.php"); ?>

<script src="jquery/jquery.js"></script>
<script src="static/js/form_animation.js"></script>
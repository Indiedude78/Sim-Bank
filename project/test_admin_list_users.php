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

<form id="form_add_user">
    <h3>Add Client</h3>
    <label for="fname">First Name</label>
    <input type="text" id="name" />
    <label for="lname">Last Name</label>
    <input type="text" id="lname" />
    <label for="email">Email</label>
    <input type="email" id="email" />
    <label for="username">Username</label>
    <input type="text" minlength="6" id="username" />
    <label for="password">Password</label>
    <input type="password" id="password" minlength="6" />
    <label for="confirm_password">Confirm Password</label>
    <input type="password" id="confirm_password" />
    <button id="add_user">Add User</button>
</form>
<br>
<div id="fetched-users">
    <table id="users_table">
        <tr>
            <th class="table_heads">Record ID</th>
            <th class="table_heads">First Name</th>
            <th class="table_heads">Last Name</th>
            <th class="table_heads">Email</th>
            <th class="table_heads">Username</th>
            <th class="table_heads">Account Status</th>
        </tr>

    </table>
</div>

<?php require(__DIR__ . "/partials/flash.php"); ?>

<script src="jquery/jquery.js"></script>
<script src="static/js/add_users.js"></script>
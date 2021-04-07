<link rel="stylesheet" href="static/css/style.css">
<?php
//we'll be including this on most/all pages so it's a good place to include anything else we want on those pages
require_once(__DIR__ . "/../lib/helpers.php");
?>
<nav id="nav">
    <ul class="nav">
        <li><a href="home.php">Home</a></li>
        <?php if(!is_logged_in()):?>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php">Register</a></li>
        <?php endif;?>
        <?php if(is_logged_in()):?>
        <li><a href="profile.php">Profile</a></li>
            <?php if (has_role("Admin")): ?>
            <li><a href="test_create_accounts.php">Create Account</a></li>
            <li><a href="test_edit_account.php">Edit Account</a></li>
            <?php endif ?>
        <li><a href="logout.php">Logout</a></li>
        <?php endif; ?>
        
    </ul>
</nav>
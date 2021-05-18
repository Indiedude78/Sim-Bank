<link rel="stylesheet" href="static/css/updatedStyle.css">
<?php
//we'll be including this on most/all pages so it's a good place to include anything else we want on those pages
require_once(__DIR__ . "/../lib/helpers.php");
?>
<nav id="nav-main">
    <ul class="nav">
        <div id="nav-list">
            <li><a href="home.php">Home</a></li>
            <?php if (!is_logged_in()) : ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif; ?>
            <?php if (is_logged_in()) : ?>
                <li><a href="profile.php">Profile</a></li>
                <?php if (has_role("Admin")) : ?>
                    <li><a href="test_admin_search.php">Admin Search</a></li>
                    <li><a href="test_admin_list_users.php">Admin List</a></li>
                <?php endif; ?>
                <li><a href="logout.php">Logout</a></li>
            <?php endif; ?>
        </div>
        <?php if (is_logged_in()) : ?>
            <div id="nav-user">
                <h4><?php safer_echo(get_username()); ?></h4>
            </div>
        <?php endif; ?>

    </ul>
</nav>
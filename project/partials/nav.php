<link rel="icon" type="image/png" href="static/images/icon.png" />
<link rel="stylesheet" href="static/css/updatedStyle.css" />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
<link rel="preconnect" href="https://fonts.gstatic.com" />
<link href="https://fonts.googleapis.com/css2?family=Kanit&display=swap" rel="stylesheet" />
<?php
//we'll be including this on most/all pages so it's a good place to include anything else we want on those pages
require_once(__DIR__ . "/../lib/helpers.php");
?>
<nav id="nav-main">
    <ul class="nav">
        <div id="nav-list">
            <li><a href="home.php"><span class="material-icons">home</span>
                    Home
                </a>
            </li>
            <?php if (!is_logged_in()) : ?>
                <li><a href="login.php"><span class="material-icons">login</span>
                        Login
                    </a>
                </li>
                <li><a href="register.php">Register</a></li>
            <?php endif; ?>
            <?php if (is_logged_in()) : ?>
                <?php if (has_role("Admin")) : ?>
                    <li><a href="test_admin_search.php"><span class="material-icons">search</span>
                            Admin Search
                        </a>
                    </li>
                    <li><a href="test_admin_list_users.php"><span class="material-icons">list</span>
                            Admin List
                        </a>
                    </li>
                <?php endif; ?>
                <li><a href="profile.php"><span class="material-icons">account_circle</span>
                        <?php safer_echo(get_username()); ?> </a>
                </li>
                <li><a href="logout.php"><span class="material-icons">logout</span>
                        Logout
                    </a>
                </li>
            <?php endif; ?>
        </div>
    </ul>
</nav>
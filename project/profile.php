<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
require_once(__DIR__ . "/partials/dashboard.php");
?>
<?php
//Note: we have this up here, so our update happens before our get/fetch
//that way we'll fetch the updated data and have it correctly reflect on the form below
//As an exercise swap these two and see how things change
if (!is_logged_in()) {
    //this will redirect to login and kill the rest of this script (prevent it from executing)
    die(header("Location: login.php"));
}

$db = getDB();
//save data if we submitted the form
if (isset($_POST["saved"])) {
    $isValid = true;
    //check if our email changed
    $newEmail = get_email();
    if (get_email() != $_POST["email"]) {
        //TODO we'll need to check if the email is available
        $email = $_POST["email"];
        $stmt = $db->prepare("SELECT COUNT(1) as InUse from Users where email = :email");
        $stmt->execute([":email" => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $inUse = 1; //default it to a failure scenario
        if ($result && isset($result["InUse"])) {
            try {
                $inUse = intval($result["InUse"]);
            } catch (Exception $e) {
            }
        }
        if ($inUse > 0) {
            flash("Email already in use");
            //for now we can just stop the rest of the update
            $isValid = false;
        } else {
            $newEmail = $email;
        }
    }
    $newUsername = get_username();
    if (get_username() != $_POST["username"]) {
        $username = $_POST["username"];
        $stmt = $db->prepare("SELECT COUNT(1) as InUse from Users where username = :username");
        $stmt->execute([":username" => $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $inUse = 1; //default it to a failure scenario
        if ($result && isset($result["InUse"])) {
            try {
                $inUse = intval($result["InUse"]);
            } catch (Exception $e) {
            }
        }
        if ($inUse > 0) {
            flash("Username already in user");
            //for now we can just stop the rest of the update
            $isValid = false;
        } else {
            $newUsername = $username;
        }
    }
    $acc_visibility = $_POST["acc_status"];
    if ($acc_visibility != is_private()) {
        $stmt = $db->prepare("UPDATE Users set is_private = :acc_vis where id = :id");
        $r = $stmt->execute([":acc_vis" => $acc_visibility, ":id" => get_id()]);
        if ($r) {
            flash("Profile visibility updated!");
        } else {
            flash("Something went wrong");
        }
    }



    if ($isValid) {
        $stmt = $db->prepare("UPDATE Users set email = :email, username= :username where id = :id");
        $r = $stmt->execute([":email" => $newEmail, ":username" => $newUsername, ":id" => get_id()]);
        if ($r) {
            flash("Profile updated!");
        } else {
            flash("Error updating profile");
        }
        //password is optional, so check if it's even set
        //if so, then check if it's a valid reset request
        if (!empty($_POST["password"]) && !empty($_POST["confirm"])) {
            if ($_POST["password"] == $_POST["confirm"]) {
                $password = $_POST["password"];
                $hash = password_hash($password, PASSWORD_BCRYPT);
                //this one we'll do separate
                $stmt = $db->prepare("UPDATE Users set password = :password where id = :id");
                $r = $stmt->execute([":id" => get_id(), ":password" => $hash]);
                if ($r) {
                    flash("Reset password");
                } else {
                    flash("Error resetting password");
                }
            }
        }
        //fetch/select fresh data in case anything changed
        $stmt = $db->prepare("SELECT * from Users WHERE id = :id LIMIT 1");
        $stmt->execute([":id" => get_id()]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $email = $result["email"];
            $username = $result["username"];
            $fname = $result["fname"];
            $lname = $result["lname"];
            $visibility = $result["is_private"];
            //let's update our session too
            $_SESSION["user"]["email"] = $email;
            $_SESSION["user"]["username"] = $username;
            $_SESSION["user"]["fname"] = $fname;
            $_SESSION["user"]["lname"] = $lname;
            $_SESSION["user"]["is_private"] = $visibility;
        }
    } else {
        //else for $isValid, though don't need to put anything here since the specific failure will output the message
    }
}


?>

<div class="form-container">
    <h3>My Profile</h3>
    <form id="profile-form" method="POST">
        <label for="fname">First Name</label>
        <input type="text" id="fname" name="fname" value="<?php safer_echo(get_first_name()); ?>" readonly />
        <label for="lname">Last Name</label>
        <input type="text" id="lname" name="lname" value="<?php safer_echo(get_last_name()); ?>" readonly />
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php safer_echo(get_email()); ?>" readonly />
        <label for="username">Username</label>
        <input type="text" maxlength="60" id="username" name="username" value="<?php safer_echo(get_username()); ?>" />
        <label for="acc_private">Private</label>
        <input type="radio" id="acc_private" name="acc_status" value="1" />
        <label for="acc_public">Public</label>
        <input type="radio" id="acc_public" name="acc_status" value="0" />
        <!-- DO NOT PRELOAD PASSWORD-->
        <label for="pw">Password</label>
        <input type="password" name="p1" />
        <label for="cpw">Confirm Password</label>
        <input type="password" name="p2" />
        <input type="submit" name="saved" value="Save Profile" />
    </form>
</div>

<?php require(__DIR__ . "/partials/flash.php"); ?>


<script src="jquery/jquery.js"></script>
<script>
    $(document).ready(function() {
        var acc_visibilty = <?php safer_echo(is_private()); ?>;
        var $radioButtons = $('input:radio[name=acc_status]');

        if (acc_visibilty == 1 && $radioButtons.is(':checked') === false) {
            $radioButtons.filter('[value=1]').prop('checked', true);
        } else {
            $radioButtons.filter('[value=0]').prop('checked', true);
        }
    });
</script>

<script src="static/js/form_animation.js"></script>
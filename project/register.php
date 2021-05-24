<head>
    <title>Simulation Bank: Registration</title>
</head>

<?php require_once(__DIR__ . "/partials/nav.php"); //Include Navigation bar
?>

<?php
//check to see if the form is set
if (isset($_POST["register"])) {
    $username = null;
    $email = null;
    $password = null;
    $confirm = null;
    //check to see if username is set
    if (isset($_POST["username"])) {
        $username = $_POST["username"];
    }
    //check to see if email is set
    if (isset($_POST["email"])) {
        $email = $_POST["email"];
    }
    //check to see if password is set
    if (isset($_POST["password"])) {
        $password = $_POST["password"];
    }
    //check to see if confirm password is set
    if (isset($_POST["confirm"])) {
        $confirm = $_POST["confirm"];
    }
    $isValid = true;
    //If passwords match, continue
    if ($password != $confirm) {
        flash("Passwords don't match");
        $isValid = false;
    } else {
    }
    //If a field is not set, fail validation
    if (!isset($email) || !isset($password) || !isset($confirm) || !isset($username)) {
        $isValid = false;
    }

    if ($isValid) {
        $hash = password_hash($password, PASSWORD_BCRYPT); //Encrypt password

        $db = getDB(); //get DB
        if (isset($db)) {
            //Use placeholders to sanitize data
            $stmt = $db->prepare("INSERT INTO Users(username, email, password) VALUES(:username, :email, :password)");
            //Data map to put into the DB
            $params = array(":username" => $username, ":email" => $email, ":password" => $hash);
            $r = $stmt->execute($params);
            //let's just see what's returned
            //echo "db returned: " . var_export($r, true); <-- Debug message
            $e = $stmt->errorInfo();
            if ($e[0] == "00000") { //If everything works
                flash("You have successfully registered!");
                header("Location: login.php"); //Wait 2 seconds and redirect to login page and kill script
            } else {
                if ($e[0] == "23000") { //Registered email or username
                    flash("Email or username exists");
                } else {
                    flash("Something went wrong!");
                }
            }
        }
    } else {
        flash("There was a validation issue");
    }
}
?>
<!--User registration form -->
<div class="form-container">
    <form id="user-registration" class="form" method="POST">
        <h3>Register</h3>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" minlength="6" maxlength="60" value="<?php if (!isset($_POST["username"])) {
                                                                                                    echo '';
                                                                                                } else {
                                                                                                    echo $_POST["username"];
                                                                                                } ?>" required />
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php if (!isset($_POST["email"])) {
                                                                echo '';
                                                            } else {
                                                                echo $_POST["email"];
                                                            } ?>" required />
        <label for="p1">Password:</label>
        <input type="password" id="p1" name="password" minlength="8" maxlength="60" required />
        <label for="p2">Confirm Password:</label>
        <input type="password" id="p2" name="confirm" minlength="8" maxlength="60" required />
        <input type="submit" name="register" value="Register" />
        <!--Javascript error messages displayed-->
        <?php flash('<p id="error-msg"></p>'); ?>
    </form>
</div>
<!--Include Javascript for client-side validation-->
<script defer type="text/javascript" src="static/js/reg_valid.js"></script>
<?php require(__DIR__ . "/partials/flash.php"); ?>
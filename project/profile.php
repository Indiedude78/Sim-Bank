<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<?php
if (!is_logged_in()) {
    die(header("Location: login.php"));
}

$db = getDB();
if (isset($_POST["saved"])) {
    $isValid = true;

    $newEmail = get_email();
    if (get_email() != $_POST["email"]) {
        $email = $_POST["email"];
        $stmt = $db->prepare("SELECT COUNT(1) as InUse from Users where email = :email");
        $stmt->execute([":email" => $email]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $inUse = 1;
        if ($result and isset($result["InUse"])) {
            try {
                $inUse = intval($result["InUse"]);
            }
            catch (Exception $e) {

            }
        }
        if ($inUse > 0) {
            echo "<br>Email not available";
            $isValid = false;
        }
        else {
            $newEmail = $email;
        }  
    }

    $newUsername = get_username();
    if (get_username() != $_POST["username"]) {
        $username = $_POST["username"];
        $stmt = $db->prepare("SELECT COUNT(1) as InUse from Users where username = :username");
        $stmt->execute(["username" => $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $inUse = 1;
        if ($result and isset($result["InUse"])) {
            try {
                $inUse = intval($result["InUse"]);
            }
            catch (Exception $e) {

            }
        }
        if ($inUse > 0) {
            echo "<br>Username is already taken";
            $isValid = false;
        }
        else {
            $newUsername = $username;
        }
    }
   // if (isValid) {
        $stmt = $db->prepare("UPDATE Users set email = :email, username = :username where id = :id");
        $r = $stmt->execute([":email" => $newEmail, ":username" => $newUsername, ":id" => get_id()]);
        if ($r) {
            echo "<br>Updated profile";
        }
        else {
            echo "<br>Error updating profile";
        }
        if (!empty($_POST["password"]) and !empty($_POST["confirm"])) {
            if ($_POST["password"] == $_POST["confirm"]) {
                $password = $_POST["password"];
                $hash = password_hash($password, PASSWORD_BCRYPT);

                $stmt = $db->prepare("UPDATE Users set password = :password where id = :id");
                $r = $stmt->execute([":id" => get_id(), ":password" => $hash]);
                if ($r) {
                    echo "<br>Password reset successfully";
                }
                else {
                    echo "<br>Error resetting password";
                }
            }
        }

        $stmt = $db->prepare("SELECT email, username from Users WHERE id = :id LIMIT 1");
        $stmt->execute([":id" => get_id()]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $email = $result["email"];
            $username = $result["username"];
            $_SESSION["user"]["email"] = $email;
            $_SESSION["user"]["username"] = $username;
        }
   // }
    else {

    }
}

?>

<form class="user-reg" method="POST">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email"/>
    <label for="username">Username:</label>
    <input type="text" id="username" name="username"/>
    <label for="p1">Password:</label>
    <input type="password" id="p1" name="password"/>
    <label for="p2">Confirm Password:</label>
    <input type="password" id="p2" name="confirm"/>
    <input type="submit" name="saved" value="Save Profile"/>
</form>
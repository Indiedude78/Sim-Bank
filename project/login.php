<!--Include navigation bar-->
<?php require_once(__DIR__ . "/partials/nav.php"); ?>
<!--User login form-->
<form class="user-reg" method="POST">
    <label for="user-login">Email or Username:</label>
    <input type="text" id="email" name="user-login"/>
    <label for="p1">Password:</label>
    <input type="password" id="p1" name="password" required/>
    <input type="submit" name="login" value="Login"/>
</form>

<?php
if (isset($_POST["login"])) { //checl to see if form is set
    $email = null;
    $password = null;
    if ((isset($_POST["user-login"]))) { //check to see if user-login creds are set
        $email = $_POST["user-login"];
    }

    if (isset($_POST["password"])) { //check to see if user password is set
        $password = $_POST["password"];
    }
    $isValid = true;
    if ((!isset($email)) || !isset($password)) { //Fail validation if user-login creds are not set
        $isValid = false;
    }
   // if (!strpos($email, "@")) {
     //   $isValid = false;
  //      echo "<br>Invalid email<br>";
   // }
    if ($isValid) {
        $db = getDB(); //get DB if validation is successful
        if (isset($db)) {
            $stmt = $db->prepare("SELECT * from Users WHERE email = :email OR username = :email"); //sanitize data using placeholders

            $params = array(":email" => $email); //map email to its variable
            $r = $stmt->execute($params);
            //echo "db returned: " . var_export($r, true);
            $e = $stmt->errorInfo();
            if ($e[0] != "00000") { //If error exists, output error message
                echo "uh oh something went wrong";
            }
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($result && isset($result["password"])) { //check to see if userlogin data matches table data
                $password_hash_from_db = $result["password"];
                if (password_verify($password, $password_hash_from_db)) { //verify password
                    $stmt = $db->prepare("
SELECT Roles.name FROM Roles JOIN UserRoles on Roles.id = UserRoles.role_id where UserRoles.user_id = :user_id and Roles.is_active = 1 and UserRoles.is_active = 1");
                    $stmt->execute([":user_id" => $result["id"]]);
                    $roles = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    unset($result["password"]);//remove password to prevent leak beyond this page
                    //create a session for the user based on the other data pulled from the table
                    $_SESSION["user"] = $result;//save the entire result array since password is removed
                    if ($roles) {
                        $_SESSION["user"]["roles"] = $roles; //set roles if exists
                    }
                    else {
                        $_SESSION["user"]["roles"] = [];
                    }
                    //on successful login let's serve-side redirect the user to the home page with a second delay.
                    die(header("refresh:1; url: home.php"));
                }
                else {
                    echo "<br>Invalid password, please try again.<br>"; //display error
                }
            }
            else {
                echo "<br>Invalid user.<br>";
            }
        }
    }
    else {
        echo "There was a validation issue.";
    }
}
?>
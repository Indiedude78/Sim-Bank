<p>Run me in the browser from your server to try</p>
<form method="POST">
  <label for="email">Email:</label>
  <input type="email" id="email" name="email" required/><br>
 <!-- <label for="username">Username:</label>
  <input type="username" id="username" name="username" required/><br>-->
  <label for="p1">Password:</label>
  <input type="password" id="p1" name="password" required/><br>
  <label for="p2">Confirm Password:</label>
  <input type="password" id="p2" name="confirm" required/><br>
  <input type="submit" name="register" value="Register"/>
</form>

<?php
if(isset($_POST["register"])){
  $email = null;
  //$userName = null;
  $password = null;
  $confirm = null;
  if(isset($_POST["email"])){
    $email = $_POST["email"];
  }
 // if(isset($_POST["username"])) {
   //   $userName = $_POST["username"];
  //}
  if(isset($_POST["password"])){
    $password = $_POST["password"];
  }
  if(isset($_POST["confirm"])){
    $confirm = $_POST["confirm"];
  }
  $isValid = true;
  //check if passwords match on the server side
  if($password == $confirm){
    echo "Passwords match <br>"; 
  }
  else{
    echo "Passwords don't match<br>";
    $isValid = false;
  }
  if(!isset($email) || !isset($password) || !isset($confirm)){
   $isValid = false; 
  }
  //TODO other validation as desired, remember this is the last line of defense
  if($isValid){
    //Generate a hash value to store the password
    $hash = password_hash($password, PASSWORD_BCRYPT);
    require_once("db.php");
    $db = getDB();
    if(isset($db)) {
      $stmt = $db->prepare("INSERT INTO Users(email, password) VALUES(:email, :password)");
      $params = array(":email"=>$email, ":password"=>$hash);
      $r = $stmt->execute($params);
      echo "db returned:" . var_export($r, true);
      $e = $stmt->errorInfo();
      if($e[0] == "00000") {
        echo "<br>Welcome! You have registered, Please login<br>";
      }
      else {
        echo "Something went wrong: " . var_export($e, true);
      }
    } 
  }
  else{
   echo "There was a validation issue"; 
  }
}
?>

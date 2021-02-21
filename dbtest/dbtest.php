<?php
//include the db file
require("db.php");
$db = getDB();
//make sure it's set
if(isset($db)){
        //fetch sql file
        $query = file_get_contents("create_table_users.sql");
        //prepares the query safely to reduce SQL injection
        $stmt = $db->prepare($query);
        //runs the query
        $stmt->execute();
        //checks if an error info populated
        //by default it's always populated so success is if index 0 is 5 zeroes
        $e = $stmt->errorInfo();
        if($e[0] != '00000'){
                echo "Query error: " . var_export($e, true);       
        }
        else{
                echo "table created successfully";
        }
        
}
else{
        echo "there may be a problem with our connection details";
}
?>

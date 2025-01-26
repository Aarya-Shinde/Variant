<?php

$db_server = "localhost";
$db_user = "root";
$db_pass = "@pokemon1"; 
$db_name = "Variant";

// Attempt to connect to the database
$conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);

try{
    $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
}

catch(mysqli_sql_exception){
    echo"Connection Failed";
}

// Check the connection
if ($conn) {
    echo "";
}

?>

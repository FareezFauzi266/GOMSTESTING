<?php
try
{
    $user = "root";
    $pass = "";
    $dbh = new PDO('mysql:host=localhost;dbname=goms', $user, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($dbh) {
        //echo "Connected successfully!";
    }
}
catch (PDOException $e)
{
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}
?>
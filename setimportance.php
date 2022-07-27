<?

require_once("config.php");


$q1 = "UPDATE Link SET Importance = 0 WHERE Importance < 11";
mysqli_query($conn, $q1);
$q1 = "UPDATE Link SET Importance = Importance-10 WHERE Importance > 10";
mysqli_query($conn, $q1);



?>

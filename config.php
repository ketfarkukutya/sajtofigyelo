<?php
$servername = "localhost";
$dbname = "ketfarkusajto";
$username = "ketfarkusajto";
$password = "ketfarkusajto";

$searchfilters = array('mkkp','kétfarkú','kutyapárt');

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

//mysql_select_db($dbname, $conn);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to Query DB
function DBQuery ($query, $message = "")
{

	if (mysqli_query($GLOBALS["conn"], $query)) {
		echo "$message";
	} else {
		echo "Error: " . mysqli_error($GLOBALS["conn"]);
	}
}

?>

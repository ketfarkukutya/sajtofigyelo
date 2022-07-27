<?php
include('db_connect.php');

$query = "DROP TABLE Link";

DBQuery ($query, "Link table Dropped");
echo("Error description: " . mysqli_error($conn) . "<br />");

$query = "
CREATE TABLE Link (
numid INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
Link NVARCHAR(1000) NOT NULL,
Title NVARCHAR(1000) NOT NULL,
Content NVARCHAR(1000) NOT NULL,
BaseLink VARCHAR(1000) NOT NULL,
OgSiteName NVARCHAR(1000) NOT NULL,
OgTitle NVARCHAR(1000) NOT NULL,
OgDescription NVARCHAR(1000) NOT NULL,
OgImage NVARCHAR(1000) NOT NULL,
OgType NVARCHAR(1000) NOT NULL,
OgLocale NVARCHAR(1000) NOT NULL,
Importance INT(1) NOT NULL,
Created TIMESTAMP
)";

DBQuery ($query, "Link table Created");
echo("Error description: " . mysqli_error($conn) . "<br />");

?>
<?
require_once("../config.php");

function time_elapsed_string($datetime, $full = false) {
    $now = (new \DateTime())->modify('-7 hours');
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'éve',
        'm' => 'hónapja',
        'd' => 'napja',
        'h' => 'órája',
        'i' => 'perce',
        's' => 'másodperce',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v ;
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . '' : 'épp most';
}

$searchterm = "";

if (isset($_GET['s']))
{
	$searchterm = filter_input(INPUT_GET, 's', FILTER_SANITIZE_SPECIAL_CHARS);
}

if ($searchterm == "")
{
	$result = mysqli_query($conn,"
SELECT 
	Link, 
	IF(OgTitle IS NULL OR OgTitle = '',Title, OgTitle) as Title, 
	IF(OgDescription IS NULL OR OgDescription = '',Content, OgDescription) as Content, 
	BaseLink,
	Created
FROM Link 
ORDER BY Importance DESC, Created DESC");
}
else
{
	$searchtermX = strtolower($searchterm);
	$result = mysqli_query($conn,"
SELECT 
	Link, 
	IF(OgTitle IS NULL OR OgTitle = '',Title, OgTitle) as Title, 
	IF(OgDescription IS NULL OR OgDescription = '',Content, OgDescription) as Content, 
	BaseLink,
	Created
FROM Link 
WHERE
		LOWER(Title) 			LIKE '%$searchtermX%' OR 
		LOWER(Content) 			LIKE '%$searchtermX%' OR
		LOWER(OgTitle) 			LIKE '%$searchtermX%' OR
		LOWER(OgDescription) 	LIKE '%$searchtermX%' 
	ORDER BY Importance DESC, Created DESC");
}
	
if (!$result)
    echo(mysqli_error($conn));
	
if(mysqli_num_rows($result) > 0){
    echo '{"articles":[';

    $first = true;
    $row = mysqli_fetch_assoc($result);
    while($row=mysqli_fetch_row($result)){

        if($first) {
            $first = false;
        } else {
            echo ',';
        }
        echo json_encode($row);
    }
    echo ']}';
} else {
    echo '[]';
}

?>

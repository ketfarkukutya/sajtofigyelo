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
	
if (!$result)
    echo(mysqli_error($conn));

$data = '<?xml version="1.0" encoding="UTF-8" ?>';
$data .= '<rss version="2.0">';

$data .= '<channel>';
$data .= '<title>MKKP Sajtófigyelés</title>';
$data .= '<link>http://sceurpien.com/mkkp/ketfarkusajto.php</link>';
$data .= '<description>MKKP Sajtófigyelő RSS Feed</description>';

if(mysqli_num_rows($result) > 0){

    $first = true;
    $row = mysqli_fetch_assoc($result);
    while($row=mysqli_fetch_row($result))
	{
		$data .= '<item>';
		$data .= '<link>';
		$data .= $row["Link"];
		$data .= '</link>';
		$data .= '<title>';
		$data .= $row["Title"];
		$data .= '</title>';
		$data .= '<description>';
		$data .= $row["Content"];
		$data .= '</description>';
		$data .= '</item>';
	}
}
$data .= '</channel>';
$data .= '</rss> ';

header('Content-Type: application/xml');
echo $data;
?>

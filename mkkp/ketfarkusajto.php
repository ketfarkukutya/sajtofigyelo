<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="hu" lang="hu">  
<head>

<title>Kétfarkú sajtófigyelés</title>
<META http-equiv="Content-type" content="text/html; charset=utf-8">
<META charset="utf-8">
<link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

</head>
<body>
<style>

a { color: inherit; text-decoration: none; } 
a:hover { color: inherit; text-decoration: none; } 
a:visited { color: inherit; text-decoration: none; } 
.title a:visited { color: #AAAAAA; text-decoration: none; } 

body {
	font-family: 'Roboto', sans-serif;
	background: #ADADAD;
}

.main {
	background-color:#FFFFFF;
	margin: 10 auto;
	width: 90%;
	padding-left: 10px;
	padding-right: 10px;
	min-height: 85px;
}

.baselink {
	font-style: italic;
	font-size: 14px;
	padding-top: 4px;
	color: #ADADAD;
}

.title {
	font-weight: bold;
	font-size: 18px;
	padding-top: 4px;
	padding-bottom: 4px;
	color: #721E0D;
}
.content {
	font-size: 14px;
	padding-top: 4px;
	padding-bottom: 4px;
	color: #000000;
}

.image {
	float: left;
	height: 75px;
	margin-top: 5px;
	padding-right: 5px;
}

.button {
    background-color: #721E0D;
    border: none;
    color: white;
    padding: 5px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    font-size: 16px;
    cursor: pointer;
}

</style>

<div class=main style="min-height: 10px; padding-top: 5px; padding-bottom: 5px;">
	<div id="searchon" onclick="showsearch();" style="cursor: pointer; font-weight: bold; color: #ADADAD;">
	Kereső kinyitása
	</div>

	<div id="searchoff" onclick="hidesearch();" style="display: none; cursor: pointer; font-weight: bold; color: #ADADAD;">
	Kereső bezárása
	</div>
	
	<div id="search" style="display: none; width: 100%;">
		<form method="post"> 
		<input style="width: 60%;" type="text" name="searchterm" id="searchterm" placeholder="Keresés..." />
		<input class="button" type="submit" name="submit" id="submit" value="Keresés" /> 
		</form>
	</div>
</div>

<script>

function hidesearch()
{
	document.getElementById('searchon').style.display = "block";
	document.getElementById('searchoff').style.display = "none";
	document.getElementById('search').style.display = "none";
}

function showsearch()
{
	document.getElementById('searchon').style.display = "none";
	document.getElementById('searchoff').style.display = "block";
	document.getElementById('search').style.display = "block";
}

$(".searchterm").on('keyup', function (e) {
    if (e.keyCode == 13) {
        document.forms[0].submit()
    }
});

function searchall()
{
	document.getElementById('searchterm').value = "";
	window.location = window.location.href;
}

</script>

<?

if (isset($_POST['submit']))
{
	$searchterm = filter_input(INPUT_POST, 'searchterm', FILTER_SANITIZE_SPECIAL_CHARS);
	?> <script> showsearch(); document.getElementById('searchterm').value = "<? print($searchterm); ?>";</script> <?
}

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

$numofresults = 0;

if ($searchterm == "")
	$content = mysqli_query($conn,"SELECT * FROM Link ORDER BY Importance DESC, Created DESC");
else
{
	$searchtermX = strtolower($searchterm);
	$content = mysqli_query($conn,"
	SELECT * 
	FROM Link 
	WHERE 
		LOWER(Title) 			LIKE '%$searchtermX%' OR 
		LOWER(Content) 			LIKE '%$searchtermX%' OR
		LOWER(OgTitle) 			LIKE '%$searchtermX%' OR
		LOWER(OgDescription) 	LIKE '%$searchtermX%' 
	ORDER BY Importance DESC, Created DESC");
}
while($content <> null && $row = mysqli_fetch_assoc($content))
{
	$numofresults++;
	$link = $row["Link"];
	$title = $row["Title"];
	$contents = $row["Content"];
	$baselink = $row["BaseLink"];
	$created = $row["Created"];
	$createdago = time_elapsed_string($created);
	$ogtitle = $row["OgTitle"];
	$ogdescription = str_replace("&nbsp;","",$row["OgDescription"]);
	$ogimage = $row["OgImage"];

	$contenttitle = strlen($ogtitle) > 0 ? $ogtitle : $title;
	$contentdescription = strlen(trim($ogdescription)) > 0 ? $ogdescription : $contents;
	
	//print("<em>$baselink - $created</em><h4><a href=\"$link\">$title</a></h4><p>$contents</p><hr />");
	?>
	<div class="main">
	<? if (strlen($ogimage) > 0) { ?><img class="image" src="<? print("$ogimage"); ?>" /><? } ?>
	<div class="baselink"><a href="http://<? print("$baselink"); ?>" target="_blank"><? print("$baselink - $createdago"); ?></a></div>
	<div class="title"><a href="<? print("$link"); ?>" target="_blank"><? print("$contenttitle"); ?></a></div>
	<div class="content"><? print("$contentdescription"); ?></div>
	</div>
	<?
}

if ($numofresults == 0)
{
?>
<div class=main style="min-height: 10px; padding-top: 5px; padding-bottom: 5px;">
	<div id="searchon" onclick="showsearch();" style="cursor: pointer; font-weight: bold; color: #000000; width: 100%; font-size: 14px; text-align: center;">
	Nincs találat.<br />
	<span onclick="searchall();" style="color: #721E0D;">Minden találat megjelenítése</span>
	</div>
</div>
<?
}

?>

</body>
</html>

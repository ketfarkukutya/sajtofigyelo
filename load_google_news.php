<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="hu" lang="hu">  
<head>

<META http-equiv="Content-type" content="text/html; charset=utf-8">
<META charset="utf-8">

</head>
<body>

<?

require_once("config.php");

foreach ($searchfilters as $searchstring)
{
	$urlstring = "https://www.google.hu/search?=lnms&tbm=nws&tbs=qdr:d&q=";
	$urlstring .= $searchstring;
	$loadpage = file_get_contents($urlstring);	
	
	$stepcount = 11;
	
	//	print($loadpage);
	
	$parsepage = $loadpage;
	$articles = explode("<h3", $parsepage);
	
	foreach ($articles as $art)
	{
		$stepcount--;
		if ($stepcount < 10)
		{
			$data = explode("href=\"/url?q=",$art);
			$data = explode("\"", $data[1]);
			$link = "$data[0]";
			$link = iconv("ISO-8859-1","UTF-8",$link);
			
			$link = explode("sa=U",$link)[0];
			$link = substr($link,0,(strlen($link)-5));
			
			$loadarticle = file_get_contents($link);
			$loadarticleheaders = explode("</head>",$loadarticle)[0];
			$ogsitename = explode("\"",explode("<meta property=\"og:site_name\" content=\"",$loadarticleheaders)[1])[0];
			$oglocale = explode("\"",explode("<meta property=\"og:locale\" content=\"",$loadarticleheaders)[1])[0];
			$ogtype = explode("\"",explode("<meta property=\"og:type\" content=\"",$loadarticleheaders)[1])[0];
			$ogtitle = explode("\"",explode("<meta property=\"og:title\" content=\"",$loadarticleheaders)[1])[0];
			$ogdescription = explode("\"",explode("<meta property=\"og:description\" content=\"",$loadarticleheaders)[1])[0];
			$ogimage = explode("\"",explode("<meta property=\"og:image\" content=\"",$loadarticleheaders)[1])[0];
			
			$data = explode("\">",$art);
			$data = explode("</a>", $data[2]);
			$title = "$data[0]";
			
			$title = iconv("ISO-8859-1","UTF-8",$title);
			$title = str_replace("<b>","",$title);
			$title = str_replace("</b>","",$title);
			$title = str_replace("\'","",$title);
			$title = str_replace("\"","",$title);
			$title = str_replace("...","",$title);
			
			$data = explode("<div class=\"st\">",$art);
			$data = explode("</div>",$data[1]);
			$content = "$data[0]";
			
			$content = iconv("ISO-8859-1","UTF-8",$content);
			$content = str_replace("<b>","",$content);
			$content = str_replace("</b>","",$content);
			$content = str_replace("\'","",$content);
			$content = str_replace("\"","",$content);
			$content = str_replace("...","",$content);
			
			$data = explode("/",$link);
			$baselink = "$data[2]";
			
			$importance = 0;
			
			foreach ($searchfilters as $teststring)
			{
				if (strpos(strtoupper($title),strtoupper($teststring)) !== false)
				{
				$importance++;
				}
				if (strpos(strtoupper($content),strtoupper($teststring)) !== false)
				{
				$importance++;
				}
			}
			
			$importance *= 10;
			
			$importance+= $stepcount;
			
			// print("<h5><a href=\"$link\">$title</a></h5>");
			// print("content: $content<br />");
			// print("baselink: $baselink<br />");
			// print("importance: $importance<br />");
			
			$isany = 0;
			$q1 = mysqli_query($conn,"select * from Link WHERE Link = '$link' AND Title = '$title' Order By Created DESC");
			while($q1 <> null && $row = mysqli_fetch_assoc($q1))
			{
				$isany = 1;
			}
			
			$q1 = mysqli_query($conn,"select * from Link WHERE Baselink = 'zoom.hu' AND Content = '$content' Order By Created DESC");
			while($q1 <> null && $row = mysqli_fetch_assoc($q1))
			{
				$isany = 1;
			}
			
			if ($isany == 0)
			{
				$q1 = "INSERT INTO Link 
					(Link, Title, Content, BaseLink, Importance, Created, OgSiteName, OgTitle, OgDescription, OgLocale, OgImage, OgType)
					VALUES
					('$link','$title','$content','$baselink','$importance',now(), '$ogsitename', '$ogtitle', '$ogdescription', '$oglocale', '$ogimage', '$ogtype')";
				
				mysqli_query($conn, $q1);
			}
		}
	}	
}



?>

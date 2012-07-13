<?php
$base = '/';

$title_suffix = '';
$query = 'home';
$page = '../protected/pages/'.$query.'.php';
if($_SERVER['QUERY_STRING'])
{
  $query = trim($_SERVER['QUERY_STRING'],'/');
  if(!file_exists($page))
  {
    header("HTTP/1.0 404 Not Found");
    $query = '404';
  }
  $title_suffix = '&rsquo;s '.$query;
  $page = '../protected/pages/'.$query.'.php';
}

$last_modified = date("D, d M Y H:i:s \G\M\T", max(filemtime($page),filemtime(__FILE__)));
$expiration = date("D, d M Y H:i:s \G\M\T", strtotime('+1 year'));
header("Cache-Control: public, no-transform");
header("Expires: $expiration");
header("Last-Modified: $last_modified");
?>
<!DOCTYPE html> 
<html> 
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
    <title>perkee<?php echo $title_suffix?></title> 
    <link rel="shortcut icon" href="<?php echo $base?>favicon.ico" />
    <link rel="apple-touch-icon" href="<?php echo $base?>apple-touch-icon.png"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <link rel="stylesheet" type="text/css" href="<?php echo $base?>p.php" />
    <script type="text/javascript">
      var _gaq = _gaq || [];
      _gaq.push(['_setAccount', 'UA-33229953-1']);
      _gaq.push(['_trackPageview']);
    
      (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
      })();
    </script>
  </head>
  <body class="dead" id="<?php echo $query?>">
    <h1>perkee<?php echo $title_suffix?></h1>
    <div id="navigation">
      <ul>
        <li><a
            href="<?php echo $base?>"
          >home</a></li>
        <li><a
            href="http://tumblr.perk.ee"
          >blog</a></li>
        <li><a
          href="<?php echo $base?>code/"
          >code</a></li>
      </ul>
    </div>
    <div id="content"><?php
include($page);  
  ?></div>
  <script type="text/javascript" src="<?php echo $base?>scripts/p.js"></script>
  </body> 
</html>

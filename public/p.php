<?php
$cssFile   = '../protected/styles/p.css'    ;
$cacheFile = '../protected/styles/cache.css';

$last_modified = max(filemtime($cssFile),filemtime(__FILE__));

if($last_modified > filemtime($cacheFile))
{
  /*proper width for navigation list items*/
  preg_match('|<div id="navigation">.*?</div>|s',file_get_contents('index.php'), $navlist);
  
  $search = array(
  //calculated values
    '|`navItemWidth`|' => floor(100 / substr_count($navlist[0],'<li>')).'%',
  //vendor specific properties
    '/(transition|box-sizing|text-shadow|border-radius|transform)[^;]*;/' => '$0-webkit-$0-o-$0-moz-$0',
  //JV minification
    "|\n|"             => '',
    "/([:;])[ \t]/"    => '$1',
    "/[ \t]+/"         => ' ',
    '|/\*[^*]*\*/|'    => '',
    '|[; ]*([},{]) *|' => '$1'
  );
  
  $cssFile = file_get_contents($cssFile);
  $cacheContents = preg_replace(
    array_keys($search),
    array_values($search),
    $cssFile
  );
  $cacheContents = preg_replace_callback('|`\d+`|',   //run length compression
    create_function('$m',                             //for i am unstoppable
      'return str_repeat(\'A\',trim($m[0],\'`\'));'), //seriously though there were a ton of As
    $cacheContents);
  
  $lines = file('../protected/pages/code.php');
  $ids = preg_grep('/^[ \t]+<div.*class="project"/', $lines);
  $ids = array_values($ids);
  foreach($ids as $key => $value)
  {
    $cacheContents .= trim(preg_replace(array("/<.*id=\"/","/\">/"),array('body.alive>div#content.','>div#projects{left:-'.($key * 100).'%}'),$value));
  }
  date_default_timezone_set('America/New_York');
  $cacheContents .= "\n/*".date('l, Y-m-d H:i:sP').'*/';
  file_put_contents($cacheFile,$cacheContents);
}
$last_modified = date("D, d M Y H:i:s \G\M\T", $last_modified);
$expiration = date("D, d M Y H:i:s \G\M\T", strtotime('+1 year'));

header("Content-type: text/css");
header("Cache-Control: public, no-transform");
header("Expires: $expiration");
header("Last-Modified: $last_modified");
include($cacheFile);
echo("\n".'/*Font Awesome-http://fortawesome.github.com/Font-Awesome CC BY 3.0 mods by perkee*/');
?>
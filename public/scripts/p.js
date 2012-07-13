<?php
$scripts = '../../protected/scripts/';

$last_modified = date("D, d M Y H:i:s \G\M\T", max(
  filemtime($scripts.'jquery.min.js'),
  filemtime($scripts.'p.js'),
  filemtime(__FILE__)
  )
);
$expiration = date("D, d M Y H:i:s \G\M\T", strtotime('+1 year'));

header("Content-type: text/javascript");
header("Cache-Control: public, no-transform");
header("Expires: $expiration");
header("Last-Modified: $last_modified");

include($scripts.'jquery.min.js');
echo("\n");
include($scripts.'p.js');
?>
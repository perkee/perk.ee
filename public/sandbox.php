<pre>
<?php
$scripts = '../protected/scripts/';

$mtimes = array(
  filemtime($scripts.'p.js'),
  filemtime($scripts.'jquery.min.js'),
  filemtime(__FILE__)
);

echo('mtimes: ');
print_r($mtimes);
echo('max: '.max($mtimes));

$last_modified = date("D, d M Y H:i:s \G\M\T", filemtime($scripts.'p.js'));
$expiration = date("D, d M Y H:i:s \G\M\T", strtotime('+1 year'));

?></pre>
<html>
<head>
 <link rel="stylesheet" href="p.php"/>
 <style>
html,body{width: 100%; height: 100%;}
<?php
$spans = '';
for($i = 0xf000; $i < 0xf0ea; ++$i)
{
  $num = dechex($i);
  echo(".$num:before{content:'\\$num'}\n");
  $spans .= "<span class=\"$num\"></span>\n";
}
?>
 </style>
</head>
<body>
<div style="font-size: 24px;">
  <i class="icon-github"></i> icon-github
</div>
<div style="font-size: 24px;">
  <i class="icon-twitter"></i> icon-twitter
</div>
<div style="font-size: 24px;">
  <i class="icon-envelope-alt"></i> icon-envelope-alt
</div>
<?php echo $spans; ?>
</body>
</html>
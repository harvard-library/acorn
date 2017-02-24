<?php
function curPageURL() {
 $pageURL = 'http';
 if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 $pageURL .= "://";
 if ($_SERVER["SERVER_PORT"] != "80") {
  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"]."/";
 } else {
  $pageURL .= $_SERVER["SERVER_NAME"]."/";
 }
 return $pageURL;
}
copy("/acorn/application/config.ini", "/acorn/application/config.ini-bak");
$reading = fopen("/acorn/application/config.ini-bak","r");
$writing = fopen("/acorn/application/config.ini","w");
while (!feof($reading)) {
  $line = fgets($reading);
  if(stristr($line,"acornurl")) {
    $line = "acornurl=".curPageURL()."\n";
    }
   fputs($writing,$line);
}
fclose($reading);
fclose($writing);
header("Location: ".curPageURL());
?>

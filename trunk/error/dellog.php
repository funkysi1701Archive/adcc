<?php
$date=date("d-m-Y_H-i-s");
$to="funkysi1701@googlemail.com";
$filename = '@@path@@/error/log.csv';
$file = fopen( $filename, "r");
$content = fread($file, filesize($filename));
fclose($file);

rename($filename, '@@path@@/error/log'.$date.'.csv');
$url='@@url@@/error/log'.$date.'.csv';
mail($to,"Web Log ".$date,$url);
?>
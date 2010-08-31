<?php
	function curPageURL() {
 		$pageURL = 'http';
 		if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 		$pageURL .= "://";
 		if ($_SERVER["SERVER_PORT"] != "80") {
  			$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 		} else {
  			$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 		}
 		return $pageURL;
	}
	function curPageName() {
 		return substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1);
	}
	date_default_timezone_set('UTC');
	$auth = $_COOKIE['auth_new'];
	$ip = $_SERVER['REMOTE_ADDR'];
	$date = date("d-m-Y H:i:s");
	$filename = $_SERVER["DOCUMENT_ROOT"].'/error/log.csv';
	$file = fopen( $filename, "a");
	fwrite( $file, "\"".$date."\",\"".curPageURL()."\",\"".curPageName()."\",\"".getenv("HTTP_REFERER")."\",\"".$auth."\",\"".$ip."\",\"".$_SERVER['HTTP_USER_AGENT']."\"\n");
	fclose( $file);

?>
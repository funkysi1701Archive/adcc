<?php 

	$page="news";
	include $_SERVER["DOCUMENT_ROOT"].'/include/auth.php';
	if($perm==false) 
	{
		header( "Location:../admin/index.php" ); exit();
	}
	$id=$_GET['id'];
	$pid=$_GET['pid'];
	date_default_timezone_set('UTC');
	$date = date("Y-m-d H:i:s");
	$type=$_COOKIE['auth_new'];
	include_once $_SERVER["DOCUMENT_ROOT"].'/include/connect.php';

	$query="DELETE FROM content WHERE id='$id'";
	$query2="DELETE FROM content WHERE additional='$id'";
	if( (true) )
	{
		include $_SERVER["DOCUMENT_ROOT"].'/include/newemail.php';
		sendemail("delnews",$auth,$id);
		mysql_query($query);
		mysql_query($query2);

		header("Location:new_edit.php?id=$id");
		
	}
?>

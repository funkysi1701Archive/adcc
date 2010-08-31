<?php 
	$title=" - Membership"; 
	include $_SERVER["DOCUMENT_ROOT"].'/include/header2.php';  
?>
<body>
<?php 
	$area="membership";
	$page="membership";
	include $_SERVER["DOCUMENT_ROOT"].'/include/auth.php';
	include $_SERVER["DOCUMENT_ROOT"].'/include/menu.php'; 
?>
<div class="left-content padding">
	<a name="content"></a>
	<h2 class="middle bold">Membership</h2>
<?php
				include $_SERVER["DOCUMENT_ROOT"].'/db/dbcontent.php';	
				$ans = getcontent("content","page = 'membership'");
				$count =getcount("content","page = 'membership'");
				for ($i =0;$i<$count;$i++)
				{
					if($ans[$i]['title']=='') $hide ="hide"; else $hide ="";
					echo "<h2 class=\"bold ".$hide."\">".$ans[$i]['title']."</h2>";
					if($ans[$i]['text']=='') $hide ="hide"; else $hide ="";
					echo "<div class=\"".$hide."\">".$ans[$i]['text']."</div>";
					if ($ans[$i]['image']!=null) echo "<img class=\"location\" alt=\"".$ans[$i]['image']."\" src=\"".$ans[$i]['image']."\">";
					if($perm==true ) echo "<ul><li class=\"first edit padding\"><a href=\"../../membership/mem_edit.php?id=".$ans[$i]['id']."\">Edit Item</a></li><li class=\"prev add padding\"><a href=\"../../membership/mem_insert.php\">Insert New</a></li><li class=\"next delete padding\"><a href=\"../../membership/mem_delete.php?id=".$ans[$i]['id']."\">Delete</a></li></ul>";
				}
				if($count==0) 
				{
					if($perm==true ) echo "<ul><li class=\"prev add padding\"><a href=\"../../membership/mem_insert.php\">Insert New</a></li></ul>";
				}
?>
</div>
<?php 
				include $_SERVER["DOCUMENT_ROOT"].'/include/footer.php';
?>
</div>
</body>
</html>
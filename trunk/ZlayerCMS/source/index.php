<?php
// Zlayers' Blog, released under GPL v.3.0. Please see COPYRIGHT for further information.
	require_once("backoffice/bases.php");
	sqlConnect();
	session_start();
function truncate($value,$length,$url){
	if(strlen($value)>$length){
		$value=substr($value,0,$length);
		$n=0;
		while(substr($value,-1)!=chr(32)){
			$n++;
			$value=substr($value,0,$length-$n);
		}
		$value=$value." ...<a href='$url'><em>more</em></a>";
	}
	return $value;
}

$navbar="<ul>
                        <li><a href=".'"'."index.php?action=home&p=1".'"'.">Home</a></li>
                        <li><a href=".'"'."index.php?action=list".'"'.">List posts</a></li>
			<li><a href=".'"'."index.php?action=about".'"'.">About me</a></li>
                </ul>";




?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html>
<head>
<meta content="text/html" charset="UTF-8"  http-equiv="content-type">
<link href='file.css' type="text/css" rel="stylesheet" />
<title>Homepage</title>
</head>
<body>
<div id="container">
	<div id="header">
		<img src="images/header_logo_white.jpg" alt="zlayer.net" />
		<p>Welcome to a few personal kilobytes of web space, reserved for my thoughts, ideas and other things for share.</p>
	</div>
	<div id="navbar">	
		<?php		echo $navbar;		?>
	</div>
	<div id="content">
	<?php
		if($_GET['action']=="about") {
			$query=mysql_query("SELECT * from zlayer_cms.posts_simple where post_ID=".'"'."7".'"') or die (mysql_error());
			$row=mysql_fetch_row($query);
			echo "<h4>".$row[1]."</h4>";
			echo "<p>".$row[11]."</p>";	
			} elseif ($_GET['action']=="list") {
	?>
				 <p>
                                 <table border=0 align=center>
                                 <tr>
                                 <td>Title</td>
                                 <td>Posted on</td>
                                 </tr>
                                <?php
                                         $qr2="SELECT post_ID, title, day, month, year FROM zlayer_cms.posts_simple WHERE deleted=".'"'."0".'"'." AND hidden=".'"'."0".'"'." ORDER BY post_ID desc";
                                        $query2=mysql_query($qr2) or die(mysql_error());
                                                 while ($row=mysql_fetch_row($query2)) {
                                                echo "<tr>";
                                                echo "<td><a href=".'"'.$_SERVER['PHP_SELF']."?action=view&post_id=".$row[0].'"'." alt=".'"'."View this post".'"'.">".$row[1]."</a></td>";
                                                echo "<td>".$row[2]."/".$row[3]."/".$row[4]."/"."</td>";
                                                echo "</tr></p>";
                                        }
                                        sqlDisconnect();

                                ?>
                                        </table>
                                        </p>
	<?php

			} elseif (!(isset($_GET['action'])) OR $_GET['action']=="home") {
				$query="SELECT post_ID, title, day, month, year, content FROM zlayer_cms.posts_simple WHERE deleted=".'"'."0".'"'." AND hidden=".'"'."0".'"'." ORDER BY post_ID desc";
				$q=mysql_query($query) or die(mysql_error());
				$num_posts=mysql_num_rows($q);
				if ($num_posts <=10 ) {
					while ($row=mysql_fetch_row($q)) {
						echo "<h5>"."<a href=".'"'.$_SERVER['PHP_SELF']."?action=view&post_id=".$row[0].'"'." >".$row[1]."</a></h5>";
						echo "<h7 font-style=italic>Created on: ";
						echo $row[2]."/".$row[3]."/".$row[4]."</h7>";
						echo "<p>".truncate($row[5],200,$_SERVER['PHP_SELF']."?action=view&post_id=".$row[0].'"')."</p>";
					}
				} else {
					if ($_GET['p']=='' OR $_GET['p']=="1") {
						$max_pages=15;
						$pages=ceil($num_posts / $max_pages);
						if ($pages <= 5) {
							echo "<p class=".'"'."navpost".'"'." text-align=center font-size=x-small>";
							echo "Navigate: ";	
							for ($i=1; $i<=$pages;$i++) {
								echo "<a href=".'"'."index.php?action=home&p=".$i.'"'." >".$i."</a>";
							}
							echo "</p>";
						$query="SELECT post_ID, title, day, month, year, content FROM zlayer_cms.posts_simple WHERE deleted=".'"'."0".'"'." AND hidden=".'"'."0".'"'." ORDER BY post_ID desc LIMIT 0,".$max_pages;
						$q=mysql_query($query) or die(mysql_error());
						while ($row=mysql_fetch_row($q)) {
	                                                echo "<h5>"."<a href=".'"'.$_SERVER['PHP_SELF']."?action=view&post_id=".$row[0].'"'." >".$row[1]."</a></h5>";
        	                                        echo "<h7 font-style=italic>Created on: ";
                	                                echo $row[2]."/".$row[3]."/".$row[4]."</h7>";
                        	                        echo "<p>".truncate($row[5],200,$_SERVER['PHP_SELF']."?action=view&post_id=".$row[0].'"')."</p>";
                                	        }


						}
					} else {
						 $max_pages=15;
        	                                 $pages=ceil($num_posts / $max_pages);
		        			 echo "<p class=".'"'."navpost".'"'." text-align=center font-size=x-small>";
                                                 echo "Navigate: ";
						 for ($i=1; $i<=$pages;$i++) {
                                                        echo "<a href=".'"'."index.php?action=home&p=".$i.'"'." >".$i."</a>";
                                                 }
                                                 echo "</p>";
						$p=$_GET['p'];
						$num=($p-1)*$max_pages;
                                                $query="SELECT post_ID, title, day, month, year, content FROM zlayer_cms.posts_simple WHERE deleted=".'"'."0".'"'." AND hidden=".'"'."0".'"'." ORDER BY post_ID desc LIMIT ".$num.",".$max_pages;
                                                $q=mysql_query($query) or die(mysql_error());
                                                 while ($row=mysql_fetch_row($q)) {
                                                        echo "<h5>"."<a href=".'"'.$_SERVER['PHP_SELF']."?action=view&post_id=".$row[0].'"'." >".$row[1]."</a></h5>";
                                                        echo "<h7 font-style=italic>Created on: ";
                                                        echo $row[2]."/".$row[3]."/".$row[4]."</h7>";
                                                        echo "<p>".truncate($row[5],200,$_SERVER['PHP_SELF']."?action=view&post_id=".$row[0].'"')."</p>";
						}
					}
			}
				
			} elseif ($_GET['action']=="view") {
				$query=mysql_query("SELECT post_ID,  title, day, month, year, content from zlayer_cms.posts_simple where post_ID=".'"'.mysql_real_escape_string($_GET['post_id']).'"') or die (mysql_error());
	                        $row=mysql_fetch_row($query);
        	                echo "<h4>".$row[1]."</h4>";
				echo "<h7 font-style=italic>Created on: ";
				echo $row[2]."/".$row[3]."/".$row[4]."</h7>";
				echo "<p>".$row[5]."</p>";
			}
	?>
	</div>
	<div id="links">
		A <a href="http://www.arcturus-game.com.ua">link</a> to my friends' website. Good game coming soon there!
		
	</div>
	<div id="footer">
		<p align="center"> Zlatko �uri�, &#169; 2009</p>
	</div>
</div>
</body>
</html>

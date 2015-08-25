<?php
require_once("../bases.php");
sqlConnect();
//Temporary navbar. I plan to load this from the database later, actually.

$navbar="<a href=".'"'.$_SERVER['PHP_SELF'].'"'.">Home </a> |   <a href=".'"'.$_SERVER['PHP_SELF']."?action=new".'"'.">New post</a> | <a href=".'"'.$_SERVER['PHP_SELF']."?action=hide".'"'.">Hide/Publish </a>  |  <a href=".'"'.$_SERVER['PHP_SELF']."?action=remove&post_id=0".'"'.">Remove </a> |   <a href=".'"'.$_SERVER['PHP_SELF']."?action=edit".'"'.">Edit</a>";
session_start();
// Let's see if we're logged in, if not, go to the homepage
if ( isset($_SESSION['logged_in'])==FALSE ) { header("Location: ../index.php"); }
// The simplest solution for me to distinguish menus and actions was to $_GET-code the navbar and actions, rather then through sessions or something. Sometimes in the future I may change this to something secure.
// First, if the action is "post", it means the user has submited an article. Let's put it in the database. I split the date fields so I can sort and filter results in the database, not in php or something.
if ($_GET['action']=="post") {
// Check if empty first, we want title and content
	if ($_POST['title']=='' or $_POST['desc']=='') {
		$msg="Please enter both title and the text of the new post";
//Maybe there is at least one of these actions, let's not delete the whole article over a forgoten title
		$only_desc=1;
	} elseif ($_POST['edit']!='0'){
		$quedit="update zlayer_cms.posts_simple set title=".'"'.mysql_real_escape_string($_POST['title']).'"'.", content=".'"'.mysql_real_escape_string($_POST['desc']).'"'." WHERE post_id=".'"'.mysql_real_escape_string($_POST['edit']).'"';
		$queditq=mysql_query($quedit) or die(mysql_error());
		$msg = "Edit successful";
	} else {
		$date=getdate();
		$qr="INSERT INTO zlayer_cms.posts_simple ( title, year, month, day, weekday, hour, minute, poster_UID, content ) VALUES (".'"'.mysql_real_escape_string($_POST['title']).'"'.", ".'"'.$date[year].'"'.", ".'"'.$date[mon].'"'.", ".'"'.$date[mday].'"'.", ".'"'.$date[wday].'"'.", ".'"'.$date[hours].'"'.", ".'"'.$date[minutes].'"'.", ".'"'."1".'"'.", ".'"'.mysql_real_escape_string($_POST['desc']).'"'." )";
		$query=mysql_query($qr) or die (mysql_error());
		$msg="Post titled <em>".$_POST['title']."</em> published.";
	}
}
// If the user clicked on Hide menu, we don't have a post_id, so we just list the stuff. Elsewhere, we need to toggle the hidden button.
if ($_GET['action']=="hide" and $_GET['post_id']!='') {
//We should get the hidden switch, then toggle it.
	$qh1="SELECT hidden from zlayer_cms.posts_simple where post_ID=".'"'.mysql_real_escape_string($_GET['post_id']).'"';
	$qrh1=mysql_query($qh1) or die (mysql_error());
	if (mysql_num_rows($qrh1)==FALSE) {
		$hidemsg="No such post!";
	} else {
		$row=mysql_fetch_row($qrh1);
		if ($row[0]=='1') { $hh='0'; } else { $hh='1'; }
		$qh="UPDATE zlayer_cms.posts_simple SET hidden=".'"'.$hh.'"'." WHERE post_ID=".'"'.mysql_real_escape_string($_GET['post_id']).'"';
		$qrh=mysql_query($qh) or die(mysql_error());
		$hidemsg="Hidden status toggled!";
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta content="text/html" charset="UTF-8"  http-equiv="content-type">
<link href='file.css' type="text/css" rel="stylesheet" />
<title>Admin interface</title>
</head>
<body>
<div id="container">
	<div id="header">
	                <img src="../images/header_logo_white.jpg" alt="zlayer.net" />
		
	</div>
	<div id="navbar">
		<?php
			echo $navbar;
		?>
	</div>
	<div id="links">
		Links
	</div>
	<div id="content">
	<?php 
// Actions are, as I've stated, $_GET-based. The first action is new - lets give the user a text field to enter new post.
		if ($_GET['action']=='new') 
		{
			echo "<h3>Content</><p>";
			$forma="<form action=".'"'.$_SERVER['PHP_SELF']."?action=post".'"'." method=".'"'."POST".'"'."> Post title
			<input type=".'"'."text".'"'." name=".'"'."title".'"'." /> Text: <br> 
			<textarea name=".'"'."desc".'"'." rows=35 cols=80 />Text</textarea>
			<input type=".'"'."hidden".'"'." name=".'"'."edit".'"'." value=".'"'."0".'"'." />
			<input type=".'"'."submit".'"'." value=".'"'."Post".'"'." /> </form>";
			echo $forma;
			echo "</p>";
		}
		elseif ($_GET['action']=='post' OR $_GET['action']=='edit') { 
// If the action is post, the user has submited something, there is an appropriate message generated. I should reuse this part for edit too. First, if the action was post, the user has a message upon successful post.
//So, adding the edit action now. If we have $_GET edit and post ID, or post and only_desc, it means we already have an article and we need to load it. If not, and we have only_desc set, it means the user forgot the title.
			echo $msg;
			if ($only_desc==1) {
// If the user has submited an article with only description, let's save the content. Maybe he's written a book, but forgot a title.
				$only_desc=0;
				echo "<p>";
	                        $forma="<form action=".'"'.$_SERVER['PHP_SELF']."?action=post".'"'." method=".'"'."POST".'"'."> Post title
        	                <input type=".'"'."text".'"'." name=".'"'."title".'"'." /> Text: <br>
                	        <textarea name=".'"'."desc".'"'." rows=25 cols=80 />".$_POST['desc']."</textarea>a
				<input type=".'"'."hidden".'"'." name=".'"'."edit".'"'." value=".'"'."0".'"'." />
                        	<input type=".'"'."submit".'"'." value=".'"'."Post".'"'." /> </form>";
                        	echo $forma;
                        	echo "</p>";
			} elseif ($_GET['action']=='edit' AND $_GET['post_id']!='') {
				$queryedit="SELECT post_ID, title, content FROM zlayer_cms.posts_simple WHERE post_ID=".'"'.mysql_real_escape_string($_GET['post_id']).'"'." AND deleted!=".'"'."1".'"';
				$queryeditq=mysql_query($queryedit) or die (mysql_error());			
				$row=mysql_fetch_row($queryeditq);
				$forma="<form action=".'"'.$_SERVER['PHP_SELF']."?action=post".'"'." method=".'"'."POST".'"'."> Post title
                                <input type=".'"'."text".'"'." name=".'"'."title".'"'." value=".'"'.$row[1].'"'."/> Text: <br>
				<input type=".'"'."hidden".'"'." name=".'"'."edit".'"'." value=".'"'.$_GET['post_id'].'"'." />
                                <textarea name=".'"'."desc".'"'." rows=25 cols=80 />".$row[2]."</textarea>
                                <input type=".'"'."submit".'"'." value=".'"'."Post".'"'." /> </form>";
                                echo $forma;
                                echo "</p>";

				} else {
		?>
					<p>
					
				        <table border=1 align=center>
				        <tr>
        				<td>Article ID</td>
        				<td>Title</td>
        				<td>Posted on</td>
        				<td>Hidden</td>
        				</tr>
        			<?php
// This is the home, let's display the posts already in, LIFO.
       				         $qr2="SELECT post_ID, title, day, month, year, hidden FROM zlayer_cms.posts_simple WHERE deleted=".'"'."0".'"';
                			$query2=mysql_query($qr2) or die(mysql_error());
               					 while ($row=mysql_fetch_row($query2)) {
                			        if ($row[5]=='1') { $hidden="Yes"; } else { $hidden="No"; }
                			        echo "<tr>";
                			        echo "<td>".$row[0]."</td>";
                			        echo "<td><a href=".'"'.$_SERVER['PHP_SELF']."?action=edit&post_id=".$row[0].'"'." alt=".'"'."Edit this post".'"'.">".$row[1]."</a></td>";
                     				echo "<td>".$row[2]."/".$row[3]."/".$row[4]."</td>";
                        			echo "<td><a href=".'"'.$_SERVER['PHP_SELF']."?action=hide&post_id=".$row[0].'"'.">".$hidden."</td>";
                        			echo "</tr></p>";
                			}			
                			sqlDisconnect();
			
        			?>
        				</table>
        				</p>
				<?php


// last case scenario, only edit (no post id), we display posts so the user can choose which to edit.
		}
	}
// This should be ELSE_1
		elseif  ( ($_GET['action']=="home") OR ($_GET['action']=='') OR ($_GET['action']=="hide") ) {
			if ($_GET['action']=="home" OR $_GET['action']='') {
			?>
			<h4> Here are your past posts</h4><p>Click on the title to view/edit that post</p>
	<?php
				} elseif  ($_GET['action']=="hide") {
					?>
					<h4> Hide some of the posts</h4>
					<?php 
					if (isset($hidemsg)) {echo $hidemsg;}
				} 
	?>
	<p>
	<table border=1 align=center>
	<tr>
	<td>Article ID</td>
	<td>Title</td>
	<td>Posted on</td>
	<td>Hidden</td>
	</tr>
	<?php
// This is the home, let's display the posts already in, LIFO.
		$qr2="SELECT post_ID, title, day, month, year, hidden FROM zlayer_cms.posts_simple WHERE deleted=".'"'."0".'"';
		$query2=mysql_query($qr2) or die(mysql_error());
		while ($row=mysql_fetch_row($query2)) {
			if ($row[5]=='1') { $hidden="Yes"; } else { $hidden="No"; }
			echo "<tr>";
			echo "<td>".$row[0]."</td>";
			echo "<td><a href=".'"'.$_SERVER['PHP_SELF']."?action=edit&post_id=".$row[0].'"'." alt=".'"'."Edit this post".'"'.">".$row[1]."</a></td>";
			echo "<td>".$row[2]."/".$row[3]."/".$row[4]."</td>";
			echo "<td><a href=".'"'.$_SERVER['PHP_SELF']."?action=hide&post_id=".$row[0].'"'.">".$hidden."</td>";
			echo "</tr></p>";
		}
		sqlDisconnect();
			
	?>
	</table>
	</p>
	<?php
// This is from the ELSE_1 that checks where on the menu we are
	} elseif ($_GET['action']=='remove') {
		echo "<h4> Removing posts from site. </h4>";
		echo "<p> Please be careful when removing posts, for now the only way to retreiving them is directly from the database. In case you just want the post not to be seen, you can hide the post. Click on the post title to remove it.</p>";
		if ($_GET['post_id']!='0') {
			$queryremove="update zlayer_cms.posts_simple set deleted=".'"'."1".'"'." where post_ID=".'"'.$_GET['post_id'].'"';
			$queryremoveq=mysql_query($queryremove) or die (mysql_error());
		}
	?>
	        <p>
	        <table border=1 align=center>
	        <tr>
	        <td>Article ID</td>
	        <td>Title</td>
	        <td>Posted on</td>
	        <td>Hidden</td>
	        </tr>
	        <?php
// This is delete, let's display the posts already in, LIFO, no other links needed.
	                $qr2="SELECT post_ID, title, day, month, year, hidden FROM zlayer_cms.posts_simple WHERE deleted=".'"'."0".'"';
	                $query2=mysql_query($qr2) or die(mysql_error());
	                while ($row=mysql_fetch_row($query2)) {
	                        if ($row[5]=='1') { $hidden="Yes"; } else { $hidden="No"; }
	                        echo "<tr>";
	                        echo "<td>".$row[0]."</td>";
	                        echo "<td><a href=".'"'.$_SERVER['PHP_SELF']."?action=remove&post_id=".$row[0].'"'." alt=".'"'."Edit this post".'"'.">".$row[1]."</a></td>";
	                        echo "<td>".$row[2]."/".$row[3]."/".$row[4]."</td>";
	                        echo "<td><a href=".'"'.$_SERVER['PHP_SELF']."?action=hide&post_id=".$row[0].'"'.">".$hidden."</td>";
	                        echo "</tr>";
	                }
	                sqlDisconnect();
	
        	?>
        	</table>
        	</p>
		<?php
	} elseif ( ($_GET['action']=='edit') ) {
	echo "<h4> Edit your posts</h4>";
	?>
        <p>
        <table border=1>
        <tr>
        <td>Article ID</td>
        <td>Title</td>
        <td>Posted on</td>
        <td>Hidden</td>
        </tr>
        <?php
// This is the edit, but nothing yet selected to edit, let's display the posts already in, LIFO.
                $qr2="SELECT post_ID, title, day, month, year, hidden FROM zlayer_cms.posts_simple WHERE deleted=".'"'."0".'"';
                $query2=mysql_query($qr2) or die(mysql_error());
                while ($row=mysql_fetch_row($query2)) {
                        if ($row[5]=='1') { $hidden="Yes"; } else { $hidden="No"; }
                        echo "<tr>";
                        echo "<td>".$row[0]."</td>";
                        echo "<td><a href=".'"'.$_SERVER['PHP_SELF']."?action=edit&post_id=".$row[0].'"'." alt=".'"'."Edit this post".'"'.">".$row[1]."</a></td>";
                        echo "<td>".$row[2]."/".$row[3]."/".$row[4]."</td>";
                        echo "<td><a href=".'"'.$_SERVER['PHP_SELF']."?action=hide&post_id=".$row[0].'"'.">".$hidden."</td>";
                        echo "</tr>";
                }
                sqlDisconnect();

        ?>
        </table>
        </p>
	<?php
	}
	else { echo "Admin panel!" ; }
	?>
	</div>
	<div id="footer">
		Footer
	</div>
</div>
</body>
</html>

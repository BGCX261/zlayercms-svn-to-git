<?php
	require_once("bases.php");
	sqlConnect();
	session_start();
	if (isset($_POST['Login'])) {
		if ( $_POST['username'] != '' && $_POST ['password'] != '') {
			$qr="SELECT UID, name, password FROM users WHERE name='".mysql_real_escape_string($_POST['username'])."' AND password=md5('".mysql_real_escape_string($_POST['password'])."')";
		$query=mysql_query($qr) or die(mysql_error());
		$row=mysql_fetch_row($query);
		if ($row[1] != '') {
			$_SESSION['id']=$row[0];
			$_SESSION['logged_in']=TRUE;
			header("Location: admin.php");
			} else { $msg="Incorrect login data!"; }
		} else {
			$msg="Please enter both username and password!<br>";
			}
	}
	sqlDisconnect();
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
	<h3> Login page </h3>
	<?php if (isset($msg)) {
		echo $msg;
		unset($msg);
		}
	?>
	<form style="height: 100px;" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>" > 
	Username:  <input type="text" name="username" value="" /> <br />
	Password: <input type="password" name="password" value="" /> <br />
	<input type="submit" name="Login" value="Login" />
	</form>
</div>
</body>
</html>


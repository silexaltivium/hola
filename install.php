<?php	  

error_reporting(E_ERROR | E_WARNING | E_PARSE);

$dir = explode('/',$_SERVER['PHP_SELF']);
$dir[count($dir)-1] = '';
$dirs = implode('/',$dir);
$step = 1;

if(isset($_POST['dbhost']))
{ 
	if( ! $con = @mysql_connect( trim( $_POST['dbhost'] ), trim( $_POST['dbuser'] ), trim( $_POST['dbpass'] ) ) )
	{
		$error = '<div class="error">Could not connect using the details you provided.</div>';
	}
	elseif ( ! mysql_select_db( trim( $_POST['dbname'] ), $con ) )
	{
		$error = '<div class="error">Could not select the database you entered.</div>';
	}
	elseif( ! fopen( 'config.php', 'w' ) )
	{
		$error = '<div class="error">Config file unwrittable.</div>';
	}
	else
	{
	 $db_errors;
		$file = fopen( 'config.php', 'w' );
		$config = '$config';
		$config_data = "<?php\n\n$config = array(\n	'dbhost' => '".$_POST['dbhost']."',\n	
																																														'dbuser' => '".$_POST['dbuser']."',\n
																																														'dbpass' => '".$_POST['dbpass']."',\n
																																														'dbname' => '".$_POST['dbname']."',\n
																																														'apppath' => '".$_POST['apppath']."'\n);
																																														\n\n?>";
		fwrite($file, $config_data);
		fclose($file);	 
		//UPLOADS TABLE CREATE
		$sql = "CREATE TABLE `uploads` (
		  `id` int(11) unsigned zerofill NOT NULL AUTO_INCREMENT,
		  `name` text NOT NULL,
		  `date` datetime NOT NULL,
		  `type` text NOT NULL,
		  `url` varchar(30) NOT NULL,
		  `password` text,
		  `views` int(11) NOT NULL,
		  `downloads` int(11) DEFAULT NULL,
		  `file` text NOT NULL,
		  `size` bigint(20) NOT NULL,
		  `author` varchar(11) NOT NULL DEFAULT '',
		  `folder` varchar(11) DEFAULT '',
		  `shorturl` text,
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `url` (`url`)
		)";
		mysql_query($sql);
		$err = mysql_error();
		if($err)
			$db_errors[] =  $err;
		// USERS TABLE CREATE
		$sql = "CREATE TABLE `users` (
		  `id` int(11) unsigned zerofill NOT NULL AUTO_INCREMENT,
		  `name` varchar(30) NOT NULL DEFAULT '',
		  `password` varchar(50) NOT NULL DEFAULT '',
		  `admin` tinyint(1) NOT NULL DEFAULT '0',
		  `space` bigint(20) unsigned NOT NULL,
		  `maxspace` bigint(20) DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `UNIQUE` (`name`)
		)";
		mysql_query($sql);
		$err = mysql_error();
		if($err)
			$db_errors[] =  $err;
		
		//**********************
		// Version 1.1
		//**********************
		
		//folders
		$sql = "CREATE TABLE `folders` (
		  `id` int(11) unsigned zerofill NOT NULL AUTO_INCREMENT,
		  `name` text NOT NULL,
		  `author` varchar(11) NOT NULL DEFAULT '',
		  PRIMARY KEY (`id`)
		)";
		mysql_query($sql);
		$err = mysql_error();
		if($err)
			$db_errors[] =  $err;
			
		//meta
		$sql = "CREATE TABLE `meta` (
		  `id` int(11) unsigned zerofill NOT NULL AUTO_INCREMENT,
		  `name` varchar(30) NOT NULL DEFAULT '',
		  `value` varchar(60) NOT NULL DEFAULT '',
		  `user` varchar(11) DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `UNIQUE` (`name`)
		)";
		mysql_query($sql);
		$err = mysql_error();
		if($err)
			$db_errors[] =  $err;	
			
		//meta entries
		$sql = "INSERT INTO `meta` (`id`, `name`, `value`, `user`) VALUES
		(00000000001, 'bandwidth_up', '0', NULL),
		(00000000002, 'bandwidth_down', '0', NULL),
		(00000000003, 'space', '0', NULL);";
		mysql_query($sql);
		$err = mysql_error();
		if($err)
			$db_errors[] =  $err;	

 //settings
 $sql = "CREATE TABLE `settings` (
   `id` int(11) unsigned zerofill NOT NULL AUTO_INCREMENT,
   `email` varchar(100) NOT NULL DEFAULT '',
   `uploadlimit` bigint(20) DEFAULT NULL,
   `thumbnails` tinyint(1) NOT NULL DEFAULT '1',
   `name` varchar(60) DEFAULT NULL,
   `shorturl` tinyint(1) NOT NULL DEFAULT '1',
   `bitly_username` varchar(60) DEFAULT NULL,
   `bitly_apikey` varchar(60) DEFAULT NULL,
   `notifyregister` tinyint(1) NOT NULL DEFAULT '1',
   `allowregister` tinyint(1) NOT NULL DEFAULT '1',
   PRIMARY KEY (`id`)
 )";
 mysql_query($sql);
 $err = mysql_error();
 if($err)
 	$db_errors[] =  $err;
 	
 	
 //default settings
		$sql = "INSERT INTO `settings` (`id`, `email`, `uploadlimit`, `thumbnails`, `name`, `shorturl`, `notifyregister`, `allowregister`)
		VALUES
			(00000000001, '', NULL, 1, '', 1, 1, 1);
		";
		mysql_query($sql);
		$err = mysql_error();
		if($err)
			$db_errors[] =  $err;	
			
			
			
						
		//Create htacess file
		$apppath = $_POST['apppath'];
		$ht = fopen( '.htaccess', 'w' );
		$content = "
# BEGIN SHAREIT!


<IfModule mod_rewrite.c>

RewriteEngine On
RewriteBase $apppath

#manage page redirection
RewriteRule ^manage/$ index.php?p=manage&s=files
#register page redirection
RewriteRule ^register/$ index.php?p=manage&s=register
RewriteRule ^register$ index.php?p=manage&s=register
#file get
RewriteRule ^(\w+)$ index.php?p=file&action=view&value=$1
# thumbnail download
RewriteRule ^(\w+)/thumb$ index.php?p=file&action=view&value=$1&s=thumb
#manage users page redirection
#RewriteRule ^manage/users/$ index.php?p=manage&s=users
# download files
RewriteRule ^(\w+)/(.*\.[^./]+)$ index.php?p=file&action=download&value=$1&file=$2
# password download files
RewriteRule ^(\w+)/(.*\.[^./]+)/(\w+)$ index.php?p=file&action=download&value=$1&file=$2&password=$3

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

</IfModule>
# END SHAREIT!";
			
			fwrite($ht, $content);
			fclose($ht);	 
		$step = 2;
	}
}

if(isset($_POST['name']))
{
 $step = 2;
	require_once( 'config.php' );
	$con = mysql_connect( $config['dbhost'], $config['dbuser'], $config['dbpass'] );
	mysql_select_db( $config['dbname'], $con ); 
 //Data validation
 $error;
 if(empty($_POST['password']))
  $error = 'Please enter a password';
 if(!empty($_POST['maxupload']) && !ctype_digit($_POST['maxupload']))
  $error = 'Please enter numeric values only for max. upload limit';
 if(empty($_POST['name']))
  $error = 'Please enter a username';
 //remove white space from username
 $username = str_replace(" ","",$_POST['name']);
 //Check for illegal characters
 $valid = array('-', '_');
 if(!ctype_alnum(str_replace($valid,'',$username)))
  $error = 'Only alphanumeric characters and "-" or "_" are allowed';
 if(strlen($username) > 30)
  $error = 'Username is too big (30 characters allowed)';	
 //Check for errors 
 if(!isset($error))
 {
	 $user = array();
	 $user['name']     	= $_POST['name'];
	 $user['password'] 	= md5('_password_'.$_POST['password']);
	 $user['maxupload'] = $_POST['maxupload'] ? $_POST['maxupload'] : '';
	 $user['admin']     = 1;  
	 
	  
	 //Insert user into database
	 $query = "INSERT INTO users VALUES (NULL,
	 																																			'".$user['name']."',
	 																																			'".$user['password']."',
	 																																			'".$user['admin']."',
	 																																			'0',
	 																																			'".($user['maxupload']*1000)."')";
	 if(!$user['maxupload'])		
	 	$query = "INSERT INTO users VALUES (NULL,
	 																																			'".$user['name']."',
	 																																			'".$user['password']."',
	 																																			'".$user['admin']."',
	 																																			'0',
	 																																			NULL)";																										
	 //Save user record in database
	 mysql_query($query);
		$err = mysql_error();
		if($err)
		{
			$db_errors[] =  $err;
		}
			
		if(!$err)
		{
		 //Create meta entries
		 $id =  sprintf("%011d", mysql_insert_id());		 
		 $banddown = "downloads_".$id;
		 $bandup = "uploads_".$id;		 
		 $query = "INSERT INTO meta VALUES (NULL,'$banddown',0,'$id')";
		 mysql_query($query);
		 $query = "INSERT INTO meta VALUES (NULL,'$bandup',0,'$id')";  
		 mysql_query($query);
		 
		 $step = 3;
		}
	}
}


//Settings
if($_POST['thumbnails'])
{
 $step = 3;
 require_once( 'config.php' );
 $con = mysql_connect( $config['dbhost'], $config['dbuser'], $config['dbpass'] );
 mysql_select_db( $config['dbname'], $con ); 
 //data validation
 if(!empty($_POST['uploadlimit']) && !ctype_digit($_POST['uploadlimit']))
  $error = 'Enter numeric values only for max. file upload size limit';
 if(!empty($_POST['email']) && !validemail($_POST['email']))
  $error = 'Please enter a valid email adress';
 if(!empty($_POST['site_name']))
 {
  //Check for illegal characters
  $valid = array('-', '_',' ','!','@','"','*','.');
  if(!ctype_alnum(str_replace($valid,'',$_POST['site_name'])))
   $error =  'Only alphanumeric characters and " -_!@"* " are allowed';
 }
  
  
 if(!$error)
 {
	 $settings = array();  
	 $settings['name'] = $_POST['site_name']; 
	 $settings['email'] = $_POST['email'];
	 $settings['uploadlimit'] = $_POST['uploadlimit'];  
	 $settings['allowregister'] = $_POST['allowregister'];
	 $settings['notifyregister'] = $_POST['notifyregister'];
	 $settings['shorturl'] = $_POST['shorturl'];
	 $settings['thumbnails'] = $_POST['thumbnails'];	 
	 
	 //Save updates to database
	 $uploadlimit = "uploadlimit = '".($settings['uploadlimit']*1000)."'";
	 if(empty($settings['uploadlimit']))
	  $uploadlimit = 'uploadlimit = NULl';
	  
	 $query = "UPDATE settings SET email = '".$settings['email']."',
	                           $uploadlimit,
	                           thumbnails = '".$settings['thumbnails']."',
	                           name = '".$settings['name']."',
	                           shorturl = '".$settings['shorturl']."',
	                           notifyregister = '".$settings['notifyregister']."',
	                           allowregister = '".$settings['allowregister']."'
	                           WHERE id = 1 LIMIT 1";
	 //Save settings record in database
	 mysql_query($query);
	 $err = mysql_error();
	 if($err)
	 {
	 	$db_errors[] =  $err;
	 }
	 else
	 {
	  $path = $dirs ."manage/";
	  header("Location: $path");
	 }	 
 }
}
//*********************************
// Validate email adress
//*********************************	
function validemail($email){
	return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
}
?>

<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8" />
	




<title>Install</title>	
	<!--STYLES-->
	<link rel="stylesheet" href="application/views/css/reset.css" type="text/css">
	<link rel="stylesheet" href="application/views/css/styles.css" type="text/css">
</head>

<body id="install">

 
 
 <div id="main" class="notice container">
	 <!--Wrapper-->
	 <div id="wrapper">
	  
	  <!--Content-->
	  <div id="content" class="padding clearfix">
	  
	  <?php if((isset($error)) or (isset($db_errors))) : ?>
	  	<div id="message" class="one message invalid clearfix">
	  		<?php if(isset($error)) echo $error ?>
		  	<?php
		  	 if(isset($db_errors))
		  	 {
			  	 foreach($db_errors as $dberror)
			  	 {
			  	  echo $dberror . '<br />';
			  	 }
		  	 }	  	 
		  	?>
	  	</div>	  	
	  <?php endif ?>
	  
	  	
		  <?php if($step == 1) : ?>
		  	<h6>Database Configuration: </h6>
		  	<div class="separator"></div>
		   <form method="post" id="install">
		    <p>
		    	<label for="dbhost">Database Host:</label>
		    	<input type="text" name="dbhost" value="<?php echo $_POST['dbhost'] ?>">
		    </p>	   	
		   	<p>
		    	<label for="dbname">Database Name:</label>
		    	<input type="text" name="dbname" value="<?php echo $_POST['dbname'] ?>">
		    </p>
		    <p>
		    	<label for="dbuser">Database User:</label>
		    	<input type="text" name="dbuser" value="<?php echo $_POST['dbuser'] ?>">
		    </p>
		    
		    <p>   
		    	<label for="dbpass">Database Password:</label>
		    	<input type="text" name="dbpass" value="<?php echo $_POST['dbpass'] ?>">
		    </p>    
		    
		    <label for"apppath">Application Path:</label>
		    <input type="text" name="apppath" value="<?php echo $dirs ?>">
		    <input type="submit" class="button right" value="Next">
		   </form>	   
		  <?php endif ?>
		  
		  <?php if($step == 2) : ?>
		 	 <h6>Create New User: </h6>
		   <form method="post" id="install">
		 	 	<label for="name">Username:</label>
		   	<input type="text" name="name" value="<?php echo (isset($_POST['name']) ? $_POST['name'] : 'admin') ?>">
		   	<label for="name">Password:</label>
		   	<input type="text" name="password" value="<?php echo $_POST['password']; ?>">
			   <label for="name">Maxspace: <span class="help"> in Kilobytes</span></label>	   
			   <input type="text" name="maxupload" value="<?php echo $_POST['maxupload']; ?>">
			   <span class="help">leave blank for unlimited</span>   	
				  <input type="submit" class="button right" value="Next">
		   </form>	  
		  <?php endif ?>
		  <?php if($step == 3) : ?>
		   <h6>Admin Settings:</h6>
		   <div class="separator"></div>
		   <form id="install" method="post"> 
	     <!--Email-->
	     <label for="email">Send notifications emails to: <span class="help">(optional)</span></label>
	     <input name="email" type="text" value="<?php echo $_POST['email'] ?>">
	     <!--Uploadsize-->
	     <label for="uploadlimit">Max. file upload size: <span class="help">in Kilobytes (optional)</span></label>	      
	     <input name="uploadlimit" type="text" value="<?php echo $_POST['uploadlimit'] ?>">
	     <span class="clear help">leave blank for unlimited</span>     
	     <!--Name-->
	     <label for="site_name">Site name: <span class="help">displayed in manage page <span class="help">(optional)</span></span></label>
	     <input name="site_name" type="text" value="<?php echo $_POST['site_name'] ?>">
	     <!--Thumbnails-->
	    	<label for="thumbnails"><input class="check" checked="checked" type="checkbox" value="1" name="thumbnails">Generate image thumbnails</label>
	    	<!--Shorturl-->
	    	<label for="shorturl"><input id="shorturl" class="check" checked="checked" type="checkbox" value="1" name="shorturl">Generate short links</label>
	    	<!--Register-->
	    	<label for="allowregister"><input class="check" checked="checked" type="checkbox" value="1" name="allowregister">Allow new user registration</label>
	    	<!--Notify register-->
	    	<label for="notifyregister"><input class="check" checked="checked" type="checkbox" value="1" name="notifyregister">Notify when new user is registered</label>
		    <!--Submit-->
		    <input type="submit" class="button right" value="Next">
		   </form>
		  <?php endif ?>
	   
	  <!--End #content-->
	  </div>   
	 <!--End #wrapper-->
	 </div>
	<!--End .container-->
 </div>
</body>
</html>
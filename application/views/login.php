<?php
if( ! defined( '_AppPath' ) ) { exit( 'Direct access to this script is not permitted' ); }
/*
|------------------------------------------------------------
|	ShareIt v1.0
| File Sharing App
|------------------------------------------------------------
| 
| Login view, edit this file for
| appearance customization
|
|------------------------------------------------------------
*/
?>

<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8" />
	<title>Login</title>	
	<!--STYLES-->
	<link rel="stylesheet" href="<?php echo $viewsdir ?>views/css/reset.css" type="text/css">
	<link rel="stylesheet" href="<?php echo $viewsdir ?>views/css/styles.css" type="text/css">
	<!--SRIPTS-->
	<script src="<?php echo $viewsdir ?>views/plugins/jquery.js" type="text/javascript"></script>
	<script src="<?php echo $viewsdir ?>views/plugins/jquery.form.js" type="text/javascript"></script>
	<script src="<?php echo $viewsdir ?>views/plugins/core.js" type="text/javascript"></script>
	<script>
		var path = "<?php echo $path ?>";
	</script>	
</head>

<body>

 
 
 <div id="main" class="notice container">
	 <!--Wrapper-->
	 <div id="wrapper" class="notice">
	  <!--Content-->
	  <div id="content" class="padding">
	  
	   <form id="on-login" method="post">
	   
	   	<!--Username-->
	 		 <p class="placeholders">
	 		 	<label for="user">Username</label>
	 		 	<input name="user" type="text" autocomplete="off">
	 		 </p>
		   <!--Password-->
	 		 <p class="placeholders">
	 		 	<label for="password">Password</label>
	 		 	<input name="password" type="password" autocomplete="off">
	 		 </p>
	 		 <input type="submit" class="submit" name="action" value="login">	
	 		 <form id="on-login" method="post">
	 		 <?php if($settings['allowregister']) : ?>
	 		 <a class="help one" href="<?php echo $path."register/"; ?>">Register new user</a> 
	 		 <?php endif ?> 
		  </form>
		  
	   
	  <!--End #content-->
	  </div>   
	 <!--End #wrapper-->
	 </div>
	<!--End .container-->
 </div>
</body>
</html>
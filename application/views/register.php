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
	 path = '';
	</script>
</head>

<body>

 
 
 <div id="main" class="notice container">
	 <!--Wrapper-->
	 <div id="wrapper" class="notice">
	  
	  <!--Content-->
	  <div id="content" class="padding">
	  
	  <?php if($error) : ?>
	  	<div id="message" class="one message invalid clearfix" style="display: block;">	  	
	  	<?php echo $error ?>
	  	</div>	  	
	  <?php endif ?>
	  
	   <form id="on-login" method="post">
	   	<!--Username-->
	    <p class="placeholders">
	    	<label for="user">Username</label>
	    	<input name="name" type="text" autocomplete="off" value="<?php echo $_POST['name'] ?>">
	    </p>
	    <!--Password-->
	    <p class="placeholders">
	    	<label for="password">Password</label>
	    	<input name="password" type="password" autocomplete="off">
	    </p>	 		 
	    <p>
	    	<label for="robot">Are you human ? <span class="help">- how much is 2 + 3 ?</span></label>
	    	<input type="text" name="robot" value="<?php echo $_POST['robot'] ?>">
	    </p>
	    
	    
	    <input type="submit" class="submit" name="action" value="register">	
	    <a class="help one" href="<?php echo $path."manage/"; ?>">Log in</a>   
	   </form>
	   
	  <!--End #content-->
	  </div>   
	 <!--End #wrapper-->
	 </div>
	<!--End .container-->
 </div>
</body>
</html>
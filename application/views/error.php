<?php
if( ! defined( '_AppPath' ) ) { exit( 'Direct access to this script is not permitted' ); }
/*
|------------------------------------------------------------
|	ShareIt v1.0
| File Sharing App
|------------------------------------------------------------
| 
| Error view, edit this file for
| appearance customization
|
|------------------------------------------------------------
*/
?>

<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8" />
	<title>Error</title>	
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

	  
	   <?php echo $error ?>
	   
	  <!--End #content-->
	  </div>   
	 <!--End #wrapper-->
	 </div>
	<!--End .container-->
 </div>
</body>
</html>
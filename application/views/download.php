<?php
if( ! defined( '_AppPath' ) ) { exit( 'Direct access to this script is not permitted' ); }
/*
|------------------------------------------------------------
|	ShareIt v1.0
| File Sharing App
|------------------------------------------------------------
| 
| Download file view, edit this file for
| appearance customization
|
|------------------------------------------------------------
*/
?>


<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8" />
	<title><?php echo $upload['name'] ?></title>	
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
 
 <!--Wrapper-->
<div class="container file">

 <!--FileContainer-->
	<div id="fileparent">
 	<div id="file">
 		<!--Preview-->
 		<div class="inner filetype <?php echo strtolower($upload['type']); if(!$settings['thumbnails']) echo " nothumb"; ?>">	
 		
	  	<?php if($settings['thumbnails'] && $upload['type'] == 'Image') : ?>
	  	<!--Image-->
				<img src="<?php echo $path ?><?php echo $upload['url']?>/thumb">
				<?php endif ?>
 		 
 		<!--End .inner-->
 		</div>
 		
 		<!--File Title-->
 		<h1><?php echo $upload['name'] ?></h1>
 		
 	<!--End #file-->
 	</div>
 	<!--Direct Link-->
 	<?php if(!$upload['password']) : ?>
 	<a id="dirlink" href="<?php echo $dirlink ?>">Direct Link</a>
 	<?php endif ?>
 	<div id="download">
 		<!--Donwload Form-->
 		<form id="download<?php echo ($upload['password'] ? "-lock" : '');?>" method="post" action="">
 			
 			<input type="hidden" name="download" value="<?php echo $upload['url']; ?>">
 			
 			<?php if($upload['password']) : ?>
 			<!--Password-->
 		 <p class="placeholders">
 		 	<label for="password">Password</label>
 		 	<input name="password" type="text" autocomplete="off">
 		 </p>
 		 <?php endif ?>
 		 
 		 <input name="action" value="get" type="submit" id="filedownload" 
 		 class="button <?php echo ($upload['password'] ? "locked" : 'download');?>">
 		 
 		</form> 		
 	<!--End #download-->
 	</div> 	
 <!--End #fileparent-->
 </div> 
<!--End .container-->
</div>

</body>
</html>
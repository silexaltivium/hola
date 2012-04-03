<?php
if( ! defined( '_AppPath' ) ) { exit( 'Direct access to this script is not permitted' ); }
/*
|------------------------------------------------------------
|	ShareIt v1.0
| File Sharing App
|------------------------------------------------------------
| 
| File management view, edit this file for
| appearance customization
|
|------------------------------------------------------------
*/
?>

<!DOCTYPE HTML>
<html>
<head>
	<meta charset="utf-8" />
	<title><?php echo $settings['name'] ? $settings['name'] : "ShareIt" ?></title>	
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

 
 
 <div id="main" class="container">
 		 <!--Header-->
	 <div id="header" class="clearfix">
	 	<a href="#" class="left"><strong><?php echo $settings['name'] ? $settings['name'] : "ShareIt" ?></strong></a>
	  <!--Logout-->
			<form method="post" class="right">	  
	  	<input id="logout"	type="submit" name="action" value="logout">
	  <!--Logout-->
	 	</form>
	 </div>
	 <!--Wrapper-->
	 <div id="wrapper">	   
	  <!--File Categories-->
	  <ul id="categories">
	  	<li class="active"><a href="#all"><span class="icon all"></span>All</a></li>
	  	<li><a href="#image"><span class="icon image"></span>Images</a></li>  	
	  	<li><a href="#audio"><span class="icon audio"></span>Audio</a></li>
	  	<li><a href="#video"><span class="icon video"></span>Video</a></li>
	  	<li><a href="#text"><span class="icon text"></span>Text</a></li>
	  	<li><a href="#archives"><span class="icon archive"></span>Archives</a></li>
	  	<li><a href="#other"><span class="icon other"></span>Other</a></li>
	  </ul>
	  
	  <!--Content-->
	  <div id="content">	  
	   <!--Toolbar-->
	   <div id="toolbar" class="clearfix">		 
	   	<a href="#" title="Uploading..." id="loading"></a>
	   	<!--Upload-->  	
					<form class="fakefile" id="on-upload" method="post" enctype="multipart/form-data">
					 <input type="file" id="uploadfile" name="file" onchange="upload(event)">   
					 <a href="#" id="fileupload" class="item"><span class="icon upload"></span>New Upload</a>
					 <input type="hidden" name="action" value="upload">
					 <input type="hidden" name="folder" value="">
					<!--End .upload-->
					</form>
					<!--Folders--> 
					<a href="#folders" id="managefolders" class="item"><span class="icon folder"></span>Folders</a>
					<?php if($isadmin) : ?>
					<!--Users-->
					<a href="#users" id="manageusers" class="item"><span class="icon users"></span>Users</a>				
					<?php endif ?>	
					<!--Stats-->
					<a href="#statistics" id="statistics_show" class="item"><span class="icon stats"></span>Statistics</a>	
					<!--Config-->
					<a href="#settings" id="settings_show" class="item"><span class="icon config"></span>Settings</a>	
	   	<!--Space-->
	   	<a href="#" class="item" title="Used Space">
	   		<span class="icon pie"></span><span id="space">
	   			<?php echo $space ?> <?php echo $space_available ?> used
	   		</span>
	   	</a>	
	   	
	   	<div class="clearfix one"></div>  	
	   	<!--Folders-->
	   	<div id="foldersview" class="one clearfix">
	   	 <div class="folders">
					  <?php if($folders) : ?>
					 
					  <?php foreach($folders as $folder) : ?>
					  
						  <a href="#<?php echo $folder['name'] ?>" class="item folder" id="folderview-<?php echo $folder['id'] ?>">
						  	<span class="icon folder"></span>
						  	<?php echo $folder['name'] ?>		
						  </a>	  
					  <?php endforeach ?>
					  
					  <?php else: ?>
					  
					  <div id="emptyfolders" class="left">You have no folders</div>
					  
					  <?php endif ?>	  
			  	<!--End .folders-->
			  	</div> 
			   <a href="#folders" id="managefolders_show" class="manage">Manage</a>			  	
			  <!--End .folders-->
			 	</div>
	   	<!--Multiple Actions-->
	   	<form method="post" id="multiple" class="one clearfix">
	   	 <input type="hidden" name="action" value="multiple">
	   	 <input type="hidden" name="value">
	   	 <!--Action-->
	   	 <label class="left">
	   	 	With selected:
	   	 	<select class="action" name="fileasction">
	   	 	 <option value="remove">Delete</option>
	   	 	 <option value="folder">Move to folder</option>
	   	 	</select>
	   		</label>
   		 <label class="left movefolder">Folder:
	   		 <select name="movefolder" id="folderoptions">
	   		 <?php if($folders) : ?>   		  
	   		 <?php foreach($folders as $folder) : ?>	   		 
	   		  <option id="folderopt-<?php echo $folder['id'] ?>" value="<?php echo $folder['id'] ?>"><?php echo $folder['name'] ?></option>
	   		 <?php endforeach ?>
	   		 <?php endif ?>
	   		 	<option>None</option>
	   		 </select>
   		 </label>	
	   		<input type="submit" class="left button small" value="ok">
	   	<!--End #multiple-->
	   	</form>
	   <!--End #toolbar--> 
	   </div>
	   
	   

	  	<div id="message" class="one message clearfix"></div>
	   <!--Files Container-->
	   <div id="files" class="clearfix">
	    <?php if($uploads) : ?>
	     <?php foreach($uploads as $upload) : ?> 
	      <!--File-->
	      <div class="file visible <?php echo strtolower($upload['type']); ?> <?php if($upload['folder']) echo "folder-".$upload['folder']; ?>" id="<?php echo $upload['url']; ?>">
	       <input type="checkbox" class="select">
	       <span class="icon delete"></span>
	      	<!--Delete-->
	      		<form class="on-delete" method="post">
	 		 			 	<input type="hidden" name="value" value="<?php echo $upload['url']; ?>">
	 		 				<!--End .remove-->
	 		 				</form>
	 		 			<!--Inner-->
	      	<div class="inner filetype <?php echo strtolower($upload['type']); if(!$settings['thumbnails']) echo " nothumb"; ?>">	      	
	      		<?php if($settings['thumbnails'] && $upload['type'] == 'Image') : ?>
		      	<!--Image-->
	 		 				<img src="<?php echo $path ?><?php echo $upload['url']?>/thumb">
	 		 				<?php endif ?>		 				
	 		 				<!--Toolbar-->
	 		 				<div class="tools">
	 		 					<!--Downloads-->
	 		 				 <a href="#" class="item" title="<?php echo $upload['downloads'] ?> download(s) ">
	 		 				 	<span class="icon downloads"></span>
	 		 				 	<?php echo $upload['downloads'] ?>
	 		 				 <!--End .downloads-->
	 		 				 </a>	 		 				 
	 		 				 <!--Password-->
	 		 				 <a href="#" class="item lock" title="Set password"><span class="icon lock"></span></a>
	 		 				 <!--Link-->
	 		 				 <a href="#" class="item link" title="Copy link"><span class="icon link"></span></a>
	 		 				 
	 		 				 <a class="shortlink" target="_blank" <?php echo $upload['shorturl'] ? 'href="'.$upload['shorturl'].'"' : "no link"; ?>><?php echo $upload['shorturl'] ? $upload['shorturl'] : "no link"; ?></a>
	 		 				 
	 		 				 <!--Password Form-->
	 		 				 <form class="on-password" method="post" style="display: none;">
	 		 				  <input type="hidden" name="value" value="<?php echo $upload['url']; ?>">
	 		 				  <input type="text" name="password" autocomplete="off" value="<?php echo $upload['password'] ?>">
	 		 				 <!--End .on-password-->
	 		 				 </form>
	 		 				<!--End tools-->
	 		 				</div>
	 		 			<!--End .inner-->
	 		 			</div>	 		 			
	 		 			<!--Title-->
	 		 			<div class="title">
	 		 				<a target="_blank" href="../<?php echo $upload['url'] ?>" title="<?php echo $upload['name'] ?>">	 		 				
	 		 					<?php echo $upload['name'] ?> 		 					
	 		 				</a> 		 		 					 				
	 		 			</div>	      
	      <!--End .file-->
	      </div>
	     
	     <?php endforeach ?>
	     
	     <?php else : ?>
	     
	     <div id="nouploads" class="center">You don't have any uploads yet.</div>	    
	    <?php endif ?>
	   <!--End #files-->
	   </div>
	  
	   
	   
	  <!--End #content-->
	  </div>   
	 <!--End #wrapper-->
	 </div>
	<!--End .container-->
 </div>
 <!--Folders-->
 <div id="folders" class="dialog clearfix">
 	<div class="one message clearfix"></div>	
  <!--Create-->
  <div class="one_third">  
	 	<h6>Create new folder:</h6>
			<form id="on-create-folder" method="post">
		 	<input type="text" name="name" value="untitled folder">
		  <input type="hidden" name="s" value="folders">
		  <input type="submit" name="action" value="create" class="button">	   
		 </form> 
	 </div>
	 <div id="folderslist" class="two_third last">
	  
	  <?php if($folders) : ?>
	    
	  <?php foreach($folders as $folder) : ?>
	  
	  <div class="folder" id="folder-<?php echo $folder['id'] ?>">
	  	<a href="#" title="Delete" class="remove"><span class="icon trash"></span></a>
	  	<?php echo $folder['name'] ?>
	  	
	   <!--Delete-->
	   <form class="on-delete-folder" method="post">
	    <input type="hidden" name="s" value="folders">
	    <input type="hidden" name="value" value="<?php echo $folder['id'] ?>">
	    <input type="hidden" name="action" value="delete">
	   </form>	  	
	  </div>
	  
	  <?php endforeach ?>
	   
	  <?php endif ?>
	  
	 </div>
 </div>
 <!--Settings-->
 <div id="settings" class="dialog settings clearfix">
  <div class="one message clearfix"></div>
  <!--Password change-->
  <div class="one clearfix password">
  <h6>Change Password:</h6>
  <div class="separator"></div>
  	<form id="on-password-user" method="post">
  		<input type="hidden" name="s" value="users">
  		<p class="placeholders">
  			<label for="password">Enter new password here</label>
  			<input name="password" type="text" autocomplete="off">
  		</p>
  		<button value="password" type="submit" name="action" class="inline">Save</button>
  	</form>
  </div>
 	<?php if($isadmin) : ?>
   <h6>Admin Settings:</h6>
   <div class="separator"></div>
   <form class="one clearfix on-settings-update" method="post">
    <input type="hidden" name="s" value="settings"> 
	    <!--Email-->
	    <label for="email">Send notifications emails to:</label>
	    <input name="email" type="text" value="<?php echo $settings['email'] ?>">
	    <!--Uploadsize-->
	    <label for="uploadlimit">Max. file upload size: <span class="help">in Kilobytes</span></label>	      
	    <input name="uploadlimit" type="text" value="<?php echo $settings['uploadlimit']; ?>">
	    <span class="clear help">leave blank for unlimited</span>	
	    
	    <!--Name-->
	    <label for="name">Site name: <span class="help">displayed in manage page</span></label>
	    <input name="name" type="text" value="<?php echo $settings['name'] ?>">
	    <!--Thumbnails-->
	   	<label for="thumbnails">
	   		<input class="check" <?php if($settings['thumbnails']) echo 'checked="checked"'; ?> type="checkbox" value="1" name="thumbnails">
	   		Generate image thumbnails
	   	</label>
	   	<!--Shorturl-->
	   	<label for="shorturl">
	   		<input id="shorturl" class="check" <?php if($settings['shorturl']) echo 'checked="checked"'; ?> type="checkbox" value="1" name="shorturl">
	   		Generate short links
	   	</label>
	   	<!--Register-->
	   	<label for="allowregister">
	   		<input class="check" <?php if($settings['allowregister']) echo 'checked="checked"'; ?> type="checkbox" value="1" name="allowregister">
	   		Allow new user registration
	   	</label>
	   	<!--Notify register-->
	   	<label for="notifyregister">
	   		<input class="check" <?php if($settings['notifyregister']) echo 'checked="checked"'; ?> type="checkbox" value="1" name="notifyregister">
	   		Notify when new user is registered
	   	</label>	   	
	   <input type="submit" name="action" value="save" class="clearfix one button">
   </form>
  <?php endif ?> 	
 </div>
 <!--Statistics-->
 <div id="statistics" class="dialog one clearfix">
  <h6>Your account statistics: </h6>  
  <div class="separator"></div>
  <div class="clearfix">
 		<p>Amount data uploaded: <span class="value"><?php echo $statistics['bandwidth_up']; ?></span></p>
 		<p>Amount data download: <span class="value"><?php echo $statistics['bandwidth_down']; ?></span></p>
 	</div>
 	<?php if($isadmin): ?>
 	 <!--Admin statistics-->
 	 <div class="adminstats clearfix one">
 	  <h6>Global statistics: </h6>  
 	  <div class="separator"></div>
 	  <p>Uploaded Data: <span class="value"><?php echo $statistics_admin['bandwidth_up']; ?></span></p>
 	  <p>Downloaded Data: <span class="value"><?php echo $statistics_admin['bandwidth_down']; ?></span></p>
 	  <p>Used space: <span class="value"><?php echo $statistics_admin['space']; ?></span></p>
 	 </div>
 	<?php endif ?>
 </div> 
 <?php if($isadmin) : ?>
	<!--Users-->
	<div id="users" class="dialog clearfix">
		<div class="one message clearfix"></div>	  	
	 <!--Create-->
	 <div id="userscreate" class="one_third">	 
	 	<h6>Create new user: </h6>  
	 	<div class="separator"></div>
	 	<form id="on-create-user" method="post">
 	 	<label for="name">Username:</label>
   	<input type="text" name="name" value="<?php echo $_POST['name']; ?>">
   	<label for="name">Password:</label>
   	<input type="text" name="password" value="<?php echo $_POST['password']; ?>">
	   <label for="name">Maxspace: <span class="help"> in Kilobytes</span></label>	   
	   <input type="text" name="maxupload" value="<?php echo $_POST['maxupload']; ?>">
	   <span class="help">leave blank for unlimited</span>	   
	   <label for="admin"><input type="checkbox" name="admin" value="1" class="check">Is admin:</label>
	   <input type="hidden" name="s" value="users">
	   <input type="submit" name="action" value="create" class="button">	   
	  </form>
	
	 </div>
	 <div id="userslist" class="two_third last">
	 	<h6>Existing users:</h6>  
	 	<div class="separator"></div>
	 	<div id="userscontainer">
			<?php if($users): ?>   
		 <!--Users-->			  
		  <?php foreach($users as $user) : ?>
		   
		   <div class="user" id="user-<?php echo $user['id']; ?>">
			   <a href="#" title="Delete" class="remove"><span class="icon trash"></span></a>
		    <?php echo $user['name'] ?>	    
		    <div class="edit" style="display: none;">
		    	
		     <!--Update-->
		     <form class="on-update-user" method="post">
		     	<input type="hidden" name="value" value="<?php echo $user['id']; ?>">
		     	<input type="hidden" name="s" value="users">
		     	<input type="submit" name="action" value="update" class="submit">
		     	<label for="name" class="text">Maxspace:<input type="text" name="maxupload" class="text" value="<?php echo $user['maxspace']; ?>"></label>	     	
		     	<label for="admin">Is admin:<input <?php if($user['admin']) echo 'checked="checked"'; ?> type="checkbox" name="admin" value="1" class="check"></label>
		     	
		     </form>
		
		     <!--Delete-->
		     <form class="on-delete-user" method="post">			     
		      <input type="hidden" name="value" value="<?php echo $user['id']; ?>">
		      <input type="hidden" name="s" value="users">
		     </form>
		     
		    <!--End .edit-->
		   	</div>
		   </div>
		  
		  <?php endforeach ?>
		 <?php endif ?>
			</div>
	 </div>
	 
	<!--End #users-->
	</div>	  
	<?php endif ?>
	
	
</body>
</html>
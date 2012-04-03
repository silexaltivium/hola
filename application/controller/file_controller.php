<?php 
if( ! defined( '_AppPath' ) ) { exit( 'Direct access to this script is not permitted' ); }

class FileController
{
 var $app;
 var $action;
 var $value;
 function __construct($app,$action,$value)
 {
  $this->app = $app;
  $this->action = $action;
  $this->value = $value;
 }
 //*********************************
	// Load
	//********************************* 
	function load()
	{
	 $response = $this->invoke();
	  if($response)
	  {
	   $upload = $response;
	   //** Set Views Variables **	 
	   //Paths
	   $path = $this->app->path; //passes application location path to generate file links and thumbnails	 	 
	   $viewsdir =  $this->app->path . "application/"; //passes views folder location
	   //** Data collection
	   //Settings
	   $settings = $this->app->settings;
	   //Direct link
	 		$dirlink = $path . $upload['url'] . "/" . str_replace(" ","_",$upload['name']);
	 		
	   include(_AppPath.'views/download.php');
	  }
	  else
	   $this->app->functions->error("File not found");
	}
 //*********************************
	// Process
	//********************************* 
 function invoke()
 {
		//*********************************
		// File download controller	  
		//*********************************
		if($this->action == 'get')
		{
		 // Get download url from Upload model
		 $upload = $this->app->uploads->getupload($this->value);
		 $password = $_POST['password'] ? $_POST['password'] : $_GET['password'];
		 // Set download hook
		 if($upload)
		  $this->app->functions->downloadHook($upload,$password);
		}
		//*********************************
		// File View controller	  
		//*********************************
		if($this->action == 'view')
		{
		 // Get upload data based on input id
		 $upload = $this->app->uploads->getupload($this->value);
		 /**Check if thumbnail was requested**/			 
		 if($_GET['s'] == 'thumb')
		 {
		  $this->app->uploads->getThumb($upload);
		 }
		 //return upload data 
		 if($upload)
		  return $upload;
		}
		//*********************************
		// File direct download controller
		//*********************************
		if($this->action == 'download')
		{
		 //Get filename & download id
		 $id = $this->value;
		 $file = $_GET['file'];
		 // Get download url from Upload model
		 $upload = $this->app->uploads->getupload($id);
		 // Exit if file is not found
		 if(!$upload)
		  $this->app->functions->error("File not found");
		 //Get original file name
			//Make file name verifications
			$upload['name'] = str_replace(" ","_",$upload['name']);			
			if($upload['name'] != $file)
			 //No file name match, deny access
			 $this->app->functions->error("File not found");
			else
			{
				$this->app->functions->downloadHook($upload,$_GET['password'],true);
			}
		}
	}
}
?>
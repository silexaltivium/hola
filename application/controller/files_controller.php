<?php 
if( ! defined( '_AppPath' ) ) { exit( 'Direct access to this script is not permitted' ); }
class FilesController
{
 var $app;
 var $action;
 function __construct($app,$action)
 {
  $this->app = $app;
  $this->action = $action;
 }
 //*********************************
	// Load
	//********************************* 
	function load()
	{
	 $this->invoke();	
	 
	 
	 $user_info = $this->app->user->info();
		$space = $this->app->functions->size_readable($user_info['space']);	 
	 if(!is_null($user_info['maxspace']))
	  $space_available = ' / '.$this->app->functions->size_readable($user_info['maxspace']);
	  


	 
	 //** Set Views Variables **	 
	 //Paths
	 $path = $this->app->path; //passes application location path to generate file links and thumbnails	 	 
	 $viewsdir =  $this->app->path . "application/"; //passes views folder location
	 
	 //** Data collection
	 if($user_info['admin']) $isadmin = true; //set if user has admin priviledges
	 //Users 
	 if($isadmin) $users = $this->app->users->get();	//if user has admin privilideges users list will be returned
	 //Folders
	 $folders = $this->app->folders->get();	// list of all available folders for current user
	 //Uploads
	 $uploads = $this->app->uploads->getuploads(); //list of all uploads made by the current user
	 //Settings
	 $settings = $this->app->settings;
	 //Statistics
	 if($isadmin)
	 	$statistics_admin = array("bandwidth_up" => $this->app->functions->size_readable($this->app->meta->get("bandwidth_up")),
	 																				      	"bandwidth_down" => $this->app->functions->size_readable($this->app->meta->get("bandwidth_down")),
	 																				      	"space" => $this->app->functions->size_readable($this->app->meta->get("space"))
	 																				      	); 
	 
	 //User specific statistics
	 $statistics = array("bandwidth_up" => $this->app->functions->size_readable($this->app->meta->get("uploads_".$user_info['id'])),
	 																				"bandwidth_down" => $this->app->functions->size_readable($this->app->meta->get("downloads_".$user_info['id']))
	 																				);	 
	 	 
	 include(_AppPath.'views/manage.php');
	}	 
 //*********************************
	// Process
	//********************************* 
 function invoke()
 {
  //*********************************
		// File Delete
		//*********************************
		if($this->action == '')
		{
		 $uploads = $this->app->uploads->getuploads();
		 if($uploads)
		  return $uploads;
		}		
		//*********************************
		// File Password Set
		//********************************* 
		if($this->action == 'password')
		{
		 $id = $_POST['value'];
		 $password = $_POST['password'];			 
		 $this->app->uploads->setPassword($id,$password);
		 exit();
		}
		//*********************************
		// File Delete
		//********************************* 
		if($this->action == 'delete')
		{
		 $id = $_POST['value'];
		 if($id)			 
		 	$this->app->uploads->removeUpload($id);
			//check for ajax request
		 if($_POST['ajax'])
		 {
		   exit($id);
		 }
		}
		//*********************************
		// File Upload
		//********************************* 
		if($this->action == 'upload')
		{
		 //check for ajax request
		 if($_POST['ajax'])
		 {
		   $response = $this->app->uploads->newupload(true);
		   exit(json_encode($response));
		 }
		}
		//*********************************
		// Multiple file actions
		//********************************* 
		if($this->action == 'multiple')
		{
		 //Get multiple file action
		 $filesaction = $_POST['fileasction'];		 
		 //Get selected files ids
		 $files = explode(",",$_POST['value']);
		 //Perform selected action on selected files	
		 if($_POST['value'])
		 {	 
			 switch($filesaction)
			 {
			  //Set folder to selected uplodas
			  case 'folder':
				  foreach($files as $file)
				  {
				   //Set folder to each selected file
				   $this->app->uploads->setFolder($file,$_POST['movefolder']);
				  }
			  break;
			  //Remove selected uploads
			  case 'remove':
			   foreach($files as $file)
			   {
			    //remove
			    $this->app->uploads->removeUpload($file);
			   }
			  break;
			 }
		 }
		 
		 //Redirect to same page		 
		 $path = $this->app->path . "manage/";
		 header("Location: $path");
		}
		//*********************************
		// Get space
		//*********************************
		if($this->action == 'space')
		{
			$user_info = $this->app->user->info();
			$space = $this->app->functions->size_readable($user_info['space']);	 
	 	if(!is_null($user_info['maxspace']))
	  	$space_available = ' / ' .$this->app->functions->size_readable($user_info['maxspace']);	  	
		 exit($space . $space_available .' used');
		}
	}
}
?>
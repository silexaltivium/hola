<?php 
if( ! defined( '_AppPath' ) ) { exit( 'Direct access to this script is not permitted' ); }
class RegisterController
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
 function load()
 {
  //Check if new registration users is allowed
  if(!$this->app->settings['allowregister'])
  {
   //Redirect to login
   $path = $this->app->path . "manage/";
   header("Location: $path");
  }
   
  $response = $this->invoke();
  if($response)
  {
  	if($response['error'])
   	$error = $response['error'];   
  	else {
  	
  	 //check if notifications is enabled
  	 if($this->app->settings['notifyregister'])
  	  //Registration was sucessful, notify admin
  	 	$this->app->functions->notify(array("name" => $response['name']),'notifyregister',$this->app->settings['email']); 	 	
  	 	
  	 //Erase current logged in session if it exists
  	 //and this method will redirect to login page
  	 $this->app->users->logout();
  	}  	
  }
  //** Set Views Variables **
  //Paths
  $path = $this->app->path; //passes application location path to generate file links and thumbnails	 	 
  $viewsdir =  $this->app->path . "application/"; //passes views folder location  
  include(_AppPath.'views/register.php');
 }
 function invoke()
 {
		//*********************************
		// Register new user
		//********************************* 
		if($this->action == 'register')
		{
		 //Check if user is human
		 if($_POST['robot'] != 5)
		  return array("error" => "Wrong human check answer");		  
		 else
		 	return $this->app->users->create(true);
		}
	}
}
?>
<?php 
if( ! defined( '_AppPath' ) ) { exit( 'Direct access to this script is not permitted' ); }
class SettingsController
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
 	$response = $this->invoke(); 	
 	//Redirect to same page
 	$path = $this->app->path . "manage/";
 	header("Location: $path");
 }
 function invoke()
 {
		//*********************************
		// Update settings user
		//********************************* 
		if($this->action == 'save')
		{
			$response = $this->app->configuration->update();		
		 if($_POST['ajax'])
		  exit(json_encode($response));
		}
	}
}
?>
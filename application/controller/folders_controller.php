<?php 
if( ! defined( '_AppPath' ) ) { exit( 'Direct access to this script is not permitted' ); }
class FoldersController
{
 var $app;
 var $action;
 var $value;
 function __construct($app,$action,$value)
 {
  $this->app = $app;
  $this->action = $action;
  $this->value = $value;
  
  $this->invoke();
 }
 //*********************************
	// Load
	//********************************* 
	function load()
	{	 
	 $response = $this->invoke();
	}		
 //*********************************
	// Process
	//********************************* 
 function invoke()
 {
		//*********************************
		// Create new user
		//********************************* 
		if($this->action == '')
		{
		 return $this->app->users->get();		 
		}
		if($this->action == 'delete')
		{
		 $response = $this->app->folders->delete($this->value);
		 //Return 
		 exit($this->value);
		}
		if($this->action == 'update')
		{
		 $response = $this->app->users->update(true);
		 if($_POST['ajax'])
		  exit(json_encode($response));
		}
		if($this->action == 'create')
		{
		 $response = $this->app->folders->create(true);
		 if($_POST['ajax'])
		  exit(json_encode($response));
		}
	}
}
?>
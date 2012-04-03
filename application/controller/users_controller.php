<?php 
if( ! defined( '_AppPath' ) ) { exit( 'Direct access to this script is not permitted' ); }
class UsersController
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
	 //Check if user has priviledges
	 if(!$this->app->user->info('admin'))
	 	$this->app->functions->error("You don't have enough privileges to view this page");
	 
	 $response = $this->invoke();
	 
		//Check for error
	 /*if($response['error'])
	  //return error
	  $error = $response['error'];
	 
	 //Get users 	 
	 $users = $this->app->users->get();
	 include('views/users.php');*/
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
		 $response = $this->app->users->delete($this->value);
		 exit($this->value);
		}
		if($this->action == 'update')
		{
		 $response = $this->app->users->update(true);
		 if($_POST['ajax'])
		  exit(json_encode($response));
		}
		if($this->action == 'password')
		{
		 $response = $this->app->users->updatepassword();
		 if($_POST['ajax'])
		  exit(json_encode($response));
		}
		if($this->action == 'create')
		{
		 $response = $this->app->users->create(true);
		 if($_POST['ajax'])
		  exit(json_encode($response));
		 /*if(!isset($response['error']))
		  header("Location: /manage/users/");
		 else
		  return $response;*/
		}
	}
}
?>
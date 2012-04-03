<?php 
if( ! defined( '_AppPath' ) ) { exit( 'Direct access to this script is not permitted' ); }
class LoginController
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
 function invoke()
 {
		//*********************************
		// Login user
		//********************************* 
		if($this->action == 'login')
		{
		 return $this->app->users->login($_POST['user'],$_POST['password']);		 
		}
		//*********************************
		// Logout user
		//********************************* 	
		if($this->action == 'logout')
	 {
	  $this->app->users->logout();
	 }
	}
}
?>
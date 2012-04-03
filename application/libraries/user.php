<?php

if( ! defined( '_AppPath' ) ) { exit( 'Direct access to this script is not permitted.' ); }


class User
{
	public $app;
	
	public function __construct( $app )
	{
		$this->app = $app;		
	}
	//******************************
 // Check if user is logged in
 //******************************
 function loggedin()
 { 
  // get stored login session
  if($this->app->session->get_var( 'username' ) == false)
  {
   //User is not logged in, redirect to login page
   require_once( _AppPath . 'controller/login_controller.php' );
   $logincontroller = new LoginController($this->app,$this->action,$this->value);
   $response = $logincontroller->invoke();
   $path = $this->app->path;
	 	$viewsdir =  $this->app->path . "application/";
	 	
	 	$settings = $this->app->settings;
   include(_AppPath.'views/login.php');
   exit();
  }
 }
	//******************************
 // Return user info
 //****************************** 
 function info()
 {
  $info = $this->app->users->getinfo($this->app->session->get_var( 'username' ));
  return $info;
 }
}
	
?>
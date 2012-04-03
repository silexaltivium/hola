<?php 

if( ! defined( '_AppPath' ) ) { exit( 'Direct access to this script is not permitted' ); }

class Controller
{
 // Controller variables
 var $app,$mode,$action,$value;
 function __construct( $app )
 {
 	$this->app  = $app;
 	$this->mode = $_POST['p'] ? $_POST['p'] : $_GET['p'];
 	$this->action = $_POST['action'] ? $_POST['action'] : $_GET['action'];
 	$this->value = $_POST['value'] ? $_POST['value'] : $_GET['value'];

 }
 //----------------------------
	// Mode Controllers
	//----------------------------
	function invoke()
	{
	 // Check mode and call
	 // the appropiate controller
	 switch($this->mode)
	 {
	  case 'file':
	  	//Call file controller
	  	require_once( _AppPath . 'controller/file_controller.php' );
	  	$filecontroller = new FileController($this->app,$this->action,$this->value);
	  	$filecontroller->load();	  
	  break;
	  
	  case 'manage':
	   //Get subsection
	   $section = $_POST['s'] ? $_POST['s'] : $_GET['s'];
	   
	   //If user is trying to register do not check for loggin
	   if($section != 'register')
	   {
		   //Call appropiate manage controller
		  	require_once( _AppPath . 'controller/login_controller.php' );
		  	$logincontroller = new LoginController($this->app,$this->action,$this->value);
		  	$logincontroller->invoke();	  
		  	//Check for login
		  	$this->app->user->loggedin();
	  	}
	  	//Load controller
	  	include_once( _AppPath . 'controller/'.$section.'_controller.php');
	  	$class = $section.'Controller';
	  	$controller = new $class($this->app,$this->action,$this->value);
	  	$controller->load();
	  	exit();
	  break;
	 
	  case '':	  
	   header("Location: manage/");
	  break;
	 }
	}
}
?>
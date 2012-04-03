<?php

if( ! defined( '_AppPath' ) ) { exit( 'Direct access to this script is not permitted' ); }

class App
{
 private $vars = array();
 
 function __construct($config)
 {
  session_start();
  
		if( is_file( 'config.php' ) )
		{
			require_once( 'config.php' );
			
			if( ! isset( $config ) || ! is_array( $config ) )
			{
				header( 'Location: install.php' );
				exit();
			}
			else
			{
				//Set app path 
  		$this->path = $config['apppath'];
			}
		}
		else
		{
			header( 'Location: install.php');
			exit();
		} 
  //Set database conection
  $this->db = new mysqli($config['dbhost'],	$config['dbuser'], $config['dbpass'], $config['dbname'] );  
 }
 
 	# Setter and Getter for loading new objects. 
 	function __set( $index, $value )
 	{
 		$this->vars[$index] = $value;
 	}
 
 	function __get( $index )
 	{
 		return $this->vars[$index];
 	}
}

?>
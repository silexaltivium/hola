<?php
if( ! defined( '_AppPath' ) ) { exit( 'Direct access to this script is not permitted.' ); }

# Sets up and monitors session variables.

class session
{
	public $app;
	
	# Regenerate session_id on load. Prevents session fixation.
	
	public function __construct( $app )
	{
		
	}
	
	# Takes an array of variables and assigns each to a $_SESSION['var']
	# via a loop.
	
	public function add_var( $vars )
	{
		foreach( $vars as $k => $v )
		{
			$_SESSION[$k] = $v;
		}
	}
	
	# Retuns a $_SESSION['var'] based on the param provided when calling.
	# Returns false if no $_SESSION['var'] is found matching the name
	# provided.
	
	public function get_var( $name )
	{
		if( isset( $_SESSION[$name] ) )
		{
			return $_SESSION[$name];
		}
		else
		{
			return false;
		}
	}
}

?>
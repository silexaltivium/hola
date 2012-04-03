<?php
if( ! defined( '_AppPath' ) ) { exit( 'Direct access to this script is not permitted' ); }

class Configuration
{
 var $app;
 function __construct( $app )
 {
  $this->app = $app;
 }
 //******************************
 // Get settings
 //******************************
 function get()
 {
  $query = "SELECT * FROM settings LIMIT 1";
  $response = $this->app->db->query($query);
  //Check if database has records
  if ($response && $row = $response->fetch_assoc())
  {
   //Change bytes to kylobytes if max file size upload is set
   if($row['uploadlimit']) $row['uploadlimit'] = $row['uploadlimit'] / 1000;
   return $row;
  }
  else
   return false;
 }
 //******************************
 // Update settings
 //******************************
 function update()
 {  
  if(!empty($_POST['uploadlimit']) && !ctype_digit($_POST['uploadlimit']))
   $error = 'Enter numeric values only for max. file upload size limit';
  if(!empty($_POST['email']) && !$this->app->functions->validemail($_POST['email']))
   $error = 'Please enter a valid email adress';
  if($error)
  {
   return array("error" => $error);
  }	  
  $settings = array();  
  $settings['name'] = $_POST['name']; 
  $settings['email'] = $_POST['email'];
  $settings['uploadlimit'] = $_POST['uploadlimit'];  
  $settings['allowregister'] = $_POST['allowregister'];
  $settings['notifyregister'] = $_POST['notifyregister'];
  $settings['shorturl'] = $_POST['shorturl'];
  $settings['thumbnails'] = $_POST['thumbnails'];
  
  if(!empty($settings['name']))
  {
	  //Check for illegal characters
	  $valid = array('-', '_',' ','!','@','"','*','.');
	  if(!ctype_alnum(str_replace($valid,'',$settings['name'])))
	   return array("error" => 'Only alphanumeric characters and " -_!@"* " are allowed');
  }
  
  
  //Save updates to database
  $uploadlimit = "uploadlimit = '".($settings['uploadlimit']*1000)."'";
  if(empty($settings['uploadlimit']))
   $uploadlimit = 'uploadlimit = NULl';
   
  $query = "UPDATE settings SET email = '".$settings['email']."',
                            $uploadlimit,
                            thumbnails = '".$settings['thumbnails']."',
                            name = '".$settings['name']."',
                            shorturl = '".$settings['shorturl']."',
                            notifyregister = '".$settings['notifyregister']."',
                            allowregister = '".$settings['allowregister']."'
                            WHERE id = 1 LIMIT 1";
  $response = $this->app->db->query($query);
  return $response;
 }
}
?>
<?php
if( ! defined( '_AppPath' ) ) { exit( 'Direct access to this script is not permitted' ); }

class Folders
{
 var $app;
 function __construct( $app )
 {
 	$this->app = $app;
 }
 //******************************
 // Insert new folder
 //******************************
 function create($return = false)
 {
  //Data validation
  $error;
  if(empty($_POST['name']))
   $error = 'Please enter a folder name';
  if($error)
  {
   return array("error" => $error);
  }
  $folder = array();
  $folder['name'] = $_POST['name'];
  $folder['author'] = $this->app->session->get_var( 'id' );
  //Check for illegal characters
  $valid = array('-', '_',' ');
  if(!ctype_alnum(str_replace($valid,'',$folder['name'])))
   return array("error" => 'Only alphanumeric characters and "-" or "_" are allowed');
      
  //Insert user into database
  $query = "INSERT INTO folders VALUES ('".$folder['id']."',
  																																						'".$folder['name']."',
  																																						'".$folder['author']."')";
  																																																																		
  //Save folder record in database
  $result = $this->app->db->query($query);
  //Set folder id
  $folder['id'] = sprintf("%011d", $this->app->db->insert_id);
  
  //If is ajax call return upload data
  if($return == true)
   return $folder;
 }
 //******************************
 // Get folders
 //******************************
 function get()
 {
  $user = $this->app->session->get_var( 'id' );
  $query = "SELECT id,name FROM folders WHERE author = '$user'";
  $response = $this->app->db->query($query);
  //Check if database has records
  if ($response->num_rows > 0)
  {
   //Records were found
   $folders = array();
   while($row = $response->fetch_array())
   {
   	$folders[] = $row;   	
   }
   return $folders;
  }
  else return false;
 }
 //******************************
 // Delete folder
 //******************************
 function delete($id)
 {
 	$query = "DELETE FROM folders WHERE id = '$id'";
 	$this->app->db->query($query);
 }
}
?>
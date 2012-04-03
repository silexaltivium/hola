<?php
if( ! defined( '_AppPath' ) ) { exit( 'Direct access to this script is not permitted' ); }

class Meta
{
 var $app;
 function __construct( $app )
 {
 	$this->app = $app;
 }
 //******************************
 // Insert new meta value
 //******************************
 function create($key,$value,$user)
 {
  //Insert user into database
  $query = "INSERT INTO meta VALUES (NULL,'$key','$value','$user')";  																																																															
  //Save meta record in database
  $this->app->db->query($query);
 }
 //******************************
 // Get meta value
 //******************************
 function get($name)
 {
  $query = "SELECT value FROM meta WHERE name = '$name' LIMIT 1";
  $response = $this->app->db->query($query);
  //Check if database has records
		if ($response && $row = $response->fetch_assoc())
 		return $row['value'];
 	else
 	 return false;
 }
 //******************************
 // Add number to meta value
 //******************************
 function set($name,$value)
 {
  $query = "UPDATE meta SET value = '$value' WHERE name = '$name' LIMIT 1";
  $this->app->db->query($query);
 }
 //******************************
 // Numberic operations for meta value
 //******************************
 function setnumeric($name,$value,$action)
 {
  $operation = "";
  switch($action)
  {
   case 'sum':
    $operation = "+".$value;
   break;
   case 'subs':
    $operation = "-".$value;
   break;
  }
  $query = "UPDATE meta SET value = value $operation WHERE name = '$name' LIMIT 1";
  $this->app->db->query($query);
 }
 //******************************
 // Delete meta value
 //******************************
 function delete($name)
 {
 	$query = "DELETE FROM meta WHERE name = '$name'";
 	$this->app->db->query($query);
 }
}
?>
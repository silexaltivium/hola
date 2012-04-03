<?php
if( ! defined( '_AppPath' ) ) { exit( 'Direct access to this script is not permitted' ); }

class Users
{
 var $app;
 function __construct( $app )
 {
  $this->app = $app;
 }
 //******************************
 // Insert new user
 //******************************
 function create($return = false)
 {
  //Data validation
  $error;
  if(empty($_POST['password']))
   $error = 'Please enter a password';
  if(!empty($_POST['maxupload']) && !ctype_digit($_POST['maxupload']))
   $error = 'Please enter numeric values only for max. upload limit';
  if(empty($_POST['name']))
   $error = 'Please enter a username';   
  //Check for errors 
  if($error)
  {
   return array("error" => $error);
  }
  $user = array();
  $user['name']  = $this->app->db->real_escape_string($_POST['name']);  
  $user['password'] 	= md5('_password_'.$_POST['password']);
  $user['maxupload'] = $_POST['maxupload'] ? $_POST['maxupload'] : '';
  $user['admin']     = $_POST['admin'];
  
  //remove white space from username
  $user['name'] = str_replace(" ","",$user['name']);
  //Check for illegal characters
  $valid = array('-', '_');
  if(!ctype_alnum(str_replace($valid,'',$user['name'])))
   return array("error" => 'Only alphanumeric characters and "-" or "_" are allowed');
  if(strlen($user['name']) > 30)
   return array("error" => 'Username is too big (30 characters allowed)');
  
   
  //Insert user into database
  $query = "INSERT INTO users VALUES (NULL,
                                     '".$user['name']."',
                                     '".$user['password']."',
                                     '".$user['admin']."',
                                     '0',
                                     '".($user['maxupload']*1000)."')";
  if(!$user['maxupload'])		
   $query = "INSERT INTO users VALUES (NULL,
                                     '".$user['name']."',
                                     '".$user['password']."',
                                     '".$user['admin']."',
                                     '0',
                                     NULL)";																											
      
  //Save user record in database
  $result = $this->app->db->query($query);
  if(!$result)
   return array("error" => "The username you chose is taken already");
   
  //Set user id   																																			
  $user['id'] = sprintf("%011d", $this->app->db->insert_id); 
   
  //Create meta entries for new user
  $this->app->meta->create("downloads_".$user['id'],0,$user['id']); //downloads entry
  $this->app->meta->create("uploads_".$user['id'],0,$user['id']); //uplodads entry
  
  
  
  $user['password'] = '';
  //If is ajax call return upload data
  if($return == true) {
   return $user;
  }
 }
 //******************************
 // Get users
 //******************************
 function get()
 {
  $user = $this->app->session->get_var( 'id' );
  $query = "SELECT id,name, admin, space, maxspace FROM users WHERE id != '$user'";
  $response = $this->app->db->query($query);
  //Check if database has records
  if ($response->num_rows > 0)
  {
   //Records were found
   $users = array();
   while($row = $response->fetch_array())
   {
    //Change bytes to kylobytes if maxspace is set
    if($row['maxspace']) $row['maxspace'] = $row['maxspace'] / 1000;
    $users[] = $row;   	
   }
   return $users;
  }
  else return false;
 }
 function getinfo($user)
 {
  $query = "SELECT id,name, admin, space, maxspace FROM users WHERE name = '$user' LIMIT 1";
  $response = $this->app->db->query($query);
  if ($response && $row = $response->fetch_assoc())
  {
   return $row;
  }
 }
 //******************************
 // Delete user
 //******************************
 function delete($user)
 {
  $query = "DELETE FROM users WHERE id = '$user'";
  $this->app->db->query($query);
  
  //Remove user meta entries
  $this->app->meta->delete("uploads_".$user); //uplodads entry
  $this->app->meta->delete("downloads_".$user); //uplodads entry
 }
 //******************************
 // Change user password
 //******************************
 function updatepassword()
 {
  //Data validation
  $error;
  if(empty($_POST['password']))
   $error = 'Please enter a password';
  //Check for errors 
  if($error)
  {
   return array("error" => $error);
  } 
  $password = md5('_password_'.$_POST['password']);  
  $user = $this->app->session->get_var( 'id' );  
  
  $query = "UPDATE users SET password = '$password' WHERE id = '$user'";                             
  $response = $this->app->db->query($query);
  
  return $response;
 } 
 //******************************
 // Update user info
 //******************************
 function update($return = false)
 {
  //Data validation
  $error;
  if(!empty($_POST['maxupload']) && !ctype_digit($_POST['maxupload']))
   $error = 'Please enter numeric values only for max. upload limit';   
  //Check for errors 
  if($error)
  {
   return array("error" => $error);
  }
  $user = array();
  $user['id']        = $_POST['value'];
  $user['maxupload'] = $_POST['maxupload'] ? $_POST['maxupload'] : NULL;
  $user['admin']     = $_POST['admin'];  
  //Update user in database
  $query = "UPDATE users SET maxspace = '".($user['maxupload']*1000)."',
                             admin    = '".$user['admin']."'  																											
                             WHERE id = '".$user['id']."'";
  
  if(is_null($user['maxupload']))
   $query = "UPDATE users SET maxspace = NULL,
                             admin    = '".$user['admin']."'  																											
                             WHERE id = '".$user['id']."'";   
  
 
  
  $response = $this->app->db->query($query);
  
  //If is ajax call return upload data
  if($return == true)
   return $user;

 }
 //******************************
 // Update user used space
 //******************************
 function updatespace($space)
 {
  $user = $this->app->session->get_var( 'username' );
  $query = "UPDATE users SET space = '$space' WHERE name = '$user' LIMIT 1";
  $response = $this->app->db->query($query);
  if($response)
   return $response;
 }
 //******************************
 // Login user
 //******************************
 function login($user,$password)
 {
  $user = $this->app->db->real_escape_string($user);
  $password = md5('_password_'.$password);
  
  $query = "SELECT * FROM users WHERE name = '$user' AND password = '$password'";
  $response = $this->app->db->query($query);
  if ($response && $row = $response->fetch_assoc())
  {
   // Credentials matched
   $this->app->session->add_var( array( 'username' => $row['name'],'id' => $row['id'] ));
   
   if( $_SESSION ) { session_regenerate_id( true ); }
   # Redirect to dashboard
   $path = $this->app->path."manage/";
   header ("Location: $path");
  }
  else
  {
   return "Incorrect";
  }
 }
 //******************************
 // Logout user
 //******************************
 function logout()
 {
  $path = $this->app->path."manage/";
  session_destroy();
  header("Location: $path");
 }
}
?>
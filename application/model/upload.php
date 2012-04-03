<?php
if( ! defined( '_AppPath' ) ) { exit( 'Direct access to this script is not permitted' ); }

class Uploads
{
 var $app;
 function __construct( $app )
 {
  $this->app = $app;
 }
 
 
 function newupload($return = false)
 {  
  //Check for valid upload
  if($_FILES['file']['error'] != UPLOAD_ERR_OK) {		 
   $error;
   //Check for maximum file upload value error
   $error = $_FILES['file']['error'] == 1 ? 'Error: Maximum file upload size allowed by server was exceeded' : ''; 
   //Check for empty file upload
   $error = $_FILES['file']['error'] == 4 ? 'Error: No file was uploaded' : '';
   return array('error'=> $error);
  }
  if((!empty($_FILES["file"]['name'])) && ($_FILES['file']['error'] == 0))
  {
   //Temp file
   $tempFile = $_FILES['file']['tmp_name'];
   //Get file name
   $filename =  basename( $_FILES['file']['name']);
   
   //Create unique id for file   
   //************* CHANGE THIS ***************************
   // make shorter ids with letters and numbers
   $now = getdate();
   $fileId = md5(mt_rand(0,1000) . implode($now) . $filename. 'file');
   //******************************************************
   
   //Set file location
   $targetfile = $fileId.'-'.$filename;
   $targetfile = str_replace(" ","_",$targetfile);
   $finalFile =  str_replace('//','/',_UploadPath).$targetfile;
   
   //Check for file size upload limit
   //*1000 converts bytes to kylobytes
   if($this->app->settings['uploadlimit'])
   { 
    if($_FILES['file']['size'] > ($this->app->settings['uploadlimit']*1000))     
    {
     $maxsize = $this->app->functions->size_readable($this->app->settings['uploadlimit']*1000);
     return array('error'=> "You have exceeded the max. file size uplod limit ($maxsize)");
    }
   }  
   //Check if user has upload size limitations
   $user_info = $this->app->user->info();
   $currentspace = $user_info['space'];
   $filesize = $_FILES['file']['size'];
   if(!is_null($user_info['maxspace']))
   {
    //Check if user has enough space	   
    $maxspace = $user_info['maxspace'];
    $space = $maxspace - $currentspace;	   
    if(($filesize) > $space)
    {
     if(!$return)
      //Exit with not enough space error
      $this->app->functions->error("You don't have enough space to upload this file");
     else
      return array('error'=> "You don't have enough space to upload this file");
    }
   }
   if(move_uploaded_file($tempFile,$finalFile))
   {   
    //File uploaded sucessfuly
    $ext = strtolower( substr( $filename, strrpos( $filename, '.' ) + 1 ) );
    //die($ext);
    $type = 'Other';
    
    /*Image Extensions*/
    if( $ext == 'png' || $ext == 'gif' || $ext == 'jpg' || $ext == 'jpeg') { $type = 'Image'; }  
    /*Video Extensions*/
    if( $ext == 'avi' || $ext == 'mov' || $ext == 'mpg' || $ext == 'mpeg' || $ext == 'wmv' ||
        $ext == 'rm'  || $ext == 'mp4'  || $ext == '3gp') { $type = 'Video'; }
    /*Audio Extensions*/
    if( $ext == 'mp3' || $ext == 'wma' ) { $type = 'Audio'; }
    /*Archives Extensions*/
    if( $ext == 'zip' || $ext == 'rar' || $ext == '7z' || $ext == 'gz' || $ext == 'dmg' ) { $type = 'Archives'; }
    /*Text Extensions*/    
    if( $ext == 'txt') //$ext == 'doc' || $ext == 'docx' || $ext == 'pdf' || $ext == 'txt' || $ext == 'rtf' )
    { $type = 'Text'; }
      
    //Create upload object
    $upload = array();    
    $upload['name']      = $filename;
    $upload['date']      = date("Y-m-d G:i:s");
    $upload['type']      = $type;
    $upload['url']       = uniqid();
    $upload['password']  = null;
    $upload['views']     = 0;
    $upload['downloads'] = 0;
    $upload['file']      = $targetfile;
    $upload['size']      = $filesize;
    $upload['author']    = $this->app->session->get_var( 'id' );
    $upload['folder']    = $_POST['folder'];
    $upload['shorturl']  = $this->app->functions->getshortlink($upload['url']); 
    $upload['target'] = $this->app->path.$response['url']; //paremter passed to ajax call                                                      
                                                          
    //Insert Upload item into database
    $query =  "INSERT INTO uploads VALUES (NULL,
                                          '".$upload['name']."',
                                          '".$upload['date']."',
                                          '".$upload['type']."',
                                          '".$upload['url']."',
                                          '".$upload['password']."',
                                          '".$upload['views']."',
                                          '".$upload['downloads']."',
                                          '".$upload['file']."',
                                          '".$upload['size']."',
                                          '".$upload['author']."',
                                          '".$upload['folder']."',
                                          '".$upload['shorturl']."')";	
                                          
                                          
         
    //Saves record in database																																				
    $result = $this->app->db->query($query);
    
    //Create thumbnail if it is specified in the settings
    if($this->app->settings['thumbnails'])
    {
     if($type == 'Image')
     {
      //Create thumbnail class
      include( _AppPath . 'libraries/thumb.php');
      $thumb = new Thumb($this->app);     
      //Store thumbail in uploads/thumbs folder
      $filename_path = _UploadPath.$targetfile;     
      $thumb_name = _UploadPath.'thumbs/_thumb' .$targetfile;
      //generate thumbnails of 168x155
      $thumb->make_thumb($finalFile,$thumb_name,168,155);	     
      //Pass thumb source paremeter
      $upload['thumb'] = $this->app->path.$upload['url'] ."/thumb";	     
     }
    }    
    //Add file size to user space
    $this->app->users->updatespace($currentspace + $upload['size']);
    
    //Update meta space
    $this->app->meta->setnumeric("space",$upload['size'],"sum");
    //Add upload bandwidth size
    $this->app->meta->setnumeric("bandwidth_up",$upload['size'],"sum"); 
    
    //Add bandwidth stats to user
    $this->app->meta->setnumeric("uploads_".$this->app->session->get_var( 'id' ),$upload['size'],"sum");
       
    //Remove url from data for security purposes
    unset($upload['file']); 
    //Set relative date
    $upload['date'] = $this->app->functions->getRelativeTime($upload['date']);
    //If is ajax call return upload data   
    if($return == true)
     return $upload;    
   }
  }
 }
 //****************************
 // Returns thumbnail file for
 // and upload of type image
 //****************************
 function getThumb($upload)
 {
  if($this->app->settings['thumbnails'])
  {
	  //Return thumbnail
	  if($upload['type'] == 'Image')
	  {
	   //Get thumbnail
	   $thumb_path = _UploadPath . 'thumbs/_thumb' .$upload['file'];
	   if(file_exists($thumb_path)) {
	    $this->app->functions->getDownload($thumb_path,$upload['name']);
	    exit();
	   }
	   else {
	   
	    //if no thumbnail was generated is because the image dimensions are
	    //smaller than the thumbnail size within the file view OR the generate
	    //thumbnails settings was disabled	    
	    //Generate new thumbnail
	    include( _AppPath . 'libraries/thumb.php');
	    $thumb = new Thumb($this->app);     
	    //Store thumbail in uploads/thumbs folder
	    $filename_path = _UploadPath.$upload['file'];     
	    $thumb_name = _UploadPath.'thumbs/_thumb' .$upload['file'];
	    //generate thumbnails of 168x155
	    $response = $thumb->make_thumb($filename_path,$thumb_name,168,155);	     
	    //Pass thumb source paremeter
	    $thumb_path = _UploadPath . 'thumbs/_thumb' .$upload['file'];
	    if(!$response)
	     $thumb_path = _UploadPath.$upload['file'];//no thumbnail was created     
	    
	    //Return thumbnail
	    $this->app->functions->getDownload($thumb_path,$upload['name']);
	   }   
	  }
  }
  else
   exit();
 } 
 //****************************
 // Return all uploads from
 // the database for logged
 // in user
 //****************************
 function getuploads()
 {
  $user = $this->app->session->get_var( 'id' );
  $query = "SELECT * FROM uploads WHERE author = '$user' ORDER BY id DESC ";
  $response = $this->app->db->query($query);
  
  //Check if database has records
  if ($response->num_rows > 0)
  {
   //Records were found
   $uploads = array();
   while($row = $response->fetch_array())
   {
    //Get relative date
    $row['date'] = $this->app->functions->getRelativeTime($row['date']);
    $uploads[] = $row;
    //Get relative date   	
   }
   return $uploads;
  }
  else return false;
 }
 //****************************
 // Set upload folder
 //****************************
 function setFolder($id,$folder) 
 {
  $query = "UPDATE uploads SET folder = '$folder' where url = '$id' LIMIT 1"; 
  $response = $this->app->db->query($query);  
  return $response;
 }
 //****************************
 // Return upload recornd based
 // on an specific url id
 //****************************
 function getUpload($url)
 {
  $query = "SELECT * FROM uploads where url = '$url' LIMIT 1";
  $response = $this->app->db->query($query);
  //Check if database has record
  if ($response && $row = $response->fetch_assoc())
  {
   $upload = array();
   foreach($row as $key => $value)
   {
    $upload[$key] = $value;
   }
   return $upload;
  }
  return false;
 }
 //****************************
 // Creates password for an
 // uploaded file
 //****************************
 function setPassword($id,$password)
 {
  //Check if password if empty
  if(empty($password))
   $password = "";
  //if(!empty($password))
   //$password = md5('_password_'.$password);   
  //save password in database records
  $query = "UPDATE uploads SET password = '$password' where url = '$id' LIMIT 1"; 
  $response = $this->app->db->query($query);  
  return $response;
 }
 //****************************
 // Update downloaded times
 //****************************
 function setDownloads($upload)
 {
  $current = $upload['downloads'];
  $current++; //Add new download count
  $id = $upload['id'];
  $query = "UPDATE uploads SET downloads = '$current' where id = '$id' LIMIT 1"; 
  $response = $this->app->db->query($query);
  
  return $response;
 }
 //****************************
 // Delete file upload
 //****************************
 function removeUpload($id)
 {
  //Get upload data
  $upload = $this->getUpload($id);
  if($upload)
  {
    //remove from database
    $query = "DELETE FROM uploads WHERE url = '$id'";
    $response = $this->app->db->query($query); 
    //Remove file size to user space
    $user_info = $this->app->user->info();
    $currentspace = $user_info['space'];
    //Update user current space
    $this->app->users->updatespace($currentspace - $upload['size']); 
    //Update meta space usage
    $this->app->meta->setnumeric("space",$upload['size'],"subs");    
    //Unlink file
    if(file_exists(_UploadPath.$upload['file']))
      unlink(_UploadPath.$upload['file']);
    //Remove thumbnail if is image
    if(file_exists(_UploadPath .'thumbs/_thumb'.$upload['file']))
      unlink(_UploadPath .'thumbs/_thumb'.$upload['file']);
  }
 }
}
?>
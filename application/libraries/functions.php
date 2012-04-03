<?php

if( ! defined( '_AppPath' ) ) { exit( 'Direct access to this script is not permitted' ); }

class Functions
{
 var $app;
 function __construct($app)
 {
  $this->app = $app;
 }
	//----------------------------
	// Short Id Generator
	//----------------------------
 function shortID($length)
 { 	
  	$out = substr(hexdec( substr(md5($_SERVER['REMOTE_ADDR'].microtime().rand(1,100000)), 0, $length) ),0,$length);;	
  	return $out;
 }
 //*********************************
	// Display error	
	//*********************************	
	function error($msg)
	{
		$path = $this->app->path;
	 $viewsdir =  $this->app->path . "application/";
	 $error = $msg;
	 include(_AppPath."views/error.php");
	 exit();
	}
	//*********************************
	// Validate email adress
	//*********************************	
	function validemail($email){
		return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
	}
	//*********************************
	// Generate bitly short url
	//*********************************	
	function bitlyurl($url,$login,$appkey,$format = 'xml',$version = '2.0.1')
	{
	  //create the URL
	  $bitly = 'http://api.bit.ly/shorten?version='.$version.'&longUrl='.urlencode($url).'&login='.$login.'&apiKey='.$appkey.'&format='.$format;
	  
	  //get the url
	  //could also use cURL here
	  $response = file_get_contents($bitly);
	  
	  //parse depending on desired format
	  if(strtolower($format) == 'json')
	  {
	    $json = @json_decode($response,true);
	    return $json['results'][$url]['shortUrl'];
	  }
	  else //xml
	  {
	    $xml = simplexml_load_string($response);
	    return 'http://bit.ly/'.$xml->results->nodeKeyVal->hash;
	  }
	}
	//*********************************
	// Get short link
	//*********************************	
	function getshortlink($url)
	{
	 $link = 'no link';
	 if($this->app->settings['shorturl'])
	 {
	  $path = 'http://'.$_SERVER['HTTP_HOST'].$this->app->path.$url;
	  if(isset($this->app->settings['bitly_username']) && isset($this->app->settings['bitly_apikey']))
	   $link = $this->bitlyurl($path,$this->app->settings['bitly_username'],$this->app->settings['bitly_apikey'],'json');
	  else
	   $link = $this->bitlyurl($path,'proxdeveloper','R_23820d2118ab9fe74af71727651592e1','json');
	 }
	 return $link;
	}                                                       
	//*********************************
	// Get file size
	//*********************************	
	function size_readable($size, $max = null, $system = 'si', $retstring = '%01.2f %s')
	{
	 // Pick units
	 $systems['si']['prefix'] = array('B', 'K', 'MB', 'GB', 'TB', 'PB');
	 $systems['si']['size']   = 1000;
	 $systems['bi']['prefix'] = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB');
	 $systems['bi']['size']   = 1024;
	 $sys = isset($systems[$system]) ? $systems[$system] : $systems['si'];
	
	 // Max unit to display
	 $depth = count($sys['prefix']) - 1;
	 if ($max && false !== $d = array_search($max, $sys['prefix'])) {
	     $depth = $d;
	 }
	
	 // Loop
	 $i = 0;
	 while ($size >= $sys['size'] && $i < $depth) {
	     $size /= $sys['size'];
	     $i++;
	 }
	
	 return sprintf($retstring, $size, $sys['prefix'][$i]);
	}
	//****************************
 // Math file password
 //****************************
 function matchPassword($upload,$password)
 {
  //$password = md5('_password_'.$password);
  if($upload['password'])
  {
   $source = md5('_password_'.$upload['password']);
   $password = md5('_password_'.$password);
   //File is password protected
   if($source != $password)
   {
    return false;
   }
   return true;
  }
  return false;
 }
 function plural($num) {
	if ($num != 1)
		return "s";
	} 
	function getRelativeTime($date) {
		$diff = time() - strtotime($date);
		if ($diff<60)
			return "less than a minute ago";
		$diff = round($diff/60);
		if ($diff<60)
			return $diff . " minute" . $this->plural($diff) . " ago";
		$diff = round($diff/60);
		if ($diff<24)
			return $diff . " hour" . $this->plural($diff) . " ago";
		$diff = round($diff/24);
		if ($diff<7)
			return $diff . " day" . $this->plural($diff) . " ago";
		$diff = round($diff/7);
		if ($diff<4)
			return $diff . " week" . $this->plural($diff) . " ago";
		return "on " . date("F j, Y", strtotime($date));
	} 
	//****************************
	// Get File Download
	//****************************
	function getDownload($file,$filename = 'download',$force = true)
	{
	 		// required for IE, otherwise Content-disposition is ignored
		if(ini_get('zlib.output_compression'))
		  ini_set('zlib.output_compression', 'Off');
		
		// addition by Jorg Weske
		$file_extension = strtolower(substr(strrchr($file,"."),1));
		
		if ( ! file_exists( $file ) ) 
			{
			  echo "<html><title>File Not Found</title><body><h1>File not found</h1></body></html>";
			  exit;
			}
			switch( $file_extension )
			{
		  case "pdf": $ctype="application/pdf"; break;
		  case "exe": $ctype="application/octet-stream"; break;
		  case "zip": $ctype="application/zip"; break;
		  case "doc": $ctype="application/msword"; break;
		  case "xls": $ctype="application/vnd.ms-excel"; break;
		  case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
		  case "gif": $ctype="image/gif"; break;
		  case "png": $ctype="image/png"; break;
		  case "jpeg":
		  case "jpg": $ctype="image/jpg"; break;
		  default: $ctype="application/force-download";
		}
		header("Pragma: public"); // required
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private",false); // required for certain browsers 
		header("Content-Type: $ctype");
		// change, added quotes to allow spaces in filenames, by Rajkumar Singh
		if($force == true)
			header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
		header("Content-Transfer-Encoding: binary");
		header("Content-Length: ".filesize($file));
		readfile("$file");
	}
 //****************************
	// File download hook
	//****************************
	function downloadHook($upload,$password = '',$direct = false)
	{
	 $forcedownload = true;
	 if($direct == true)
	  $forcedownload = false;
	 // Check for password protection
	 if($upload['password'])
	 {
	 	if($this->matchPassword($upload,$password))
	 	{
	 	 //Check ajax request
	   if($_POST['ajax']) exit("success");
	   //Add download counter
				$this->app->uploads->setDownloads($upload);	
				//Add download bandwidth size
				$this->app->meta->setnumeric("bandwidth_down",$upload['size'],"sum"); 
	 	 // Get download file
	 	 $this->getDownload( _UploadPath.$upload['file'],
	 																										$upload['name'],$forcedownload);																						
	 		exit();
	 	}
	 	//Wrong password
	 	else
	 	{	 	 
	 	 //Check ajax request
	   if($_POST['ajax']) exit("Wrong Password");
	   else
	    $this->app->functions->error("This file is password protected");
	 	}	 	 
	 }
	 //No password proteced, get download url directly
	 else
	 {
	  //Check ajax request
	  if($_POST['ajax']) exit("success");
	  //Add download counter
			$this->app->uploads->setDownloads($upload);
			//Add download bandwidth size
			$this->app->meta->setnumeric("bandwidth_down",$upload['size'],"sum");
			//Add upload bandwidth size
			$this->app->meta->setnumeric("downloads_".$upload['author'],$upload['size'],"sum");
			//Get download file
	  $this->getDownload( _UploadPath.$upload['file'],
	 																										$upload['name'],$forcedownload);  
	  exit();
	 }	 
	}	
	//****************************
	// Email notification
	//****************************
	function notify( $args, $type, $to )
	{
		# Sends notification emails. Each notification has its own subject and
		# and message body as displayed below. You can change the contents of
		# the email, make sure you don't alter any of the PHP.
		
		$subject = '';
		
		switch( $type )
		{			
			case 'register' :
				$subject = 'User Registration - ' . $this->app->settings['name'];
				$message = 'You have been registered as a user on '.$this->app->settings['name'].'. Your login information is below.<br /><br />
							Username: '.$args['user'].'<br />Password: '.$args['password'].'<br /><br />Open the following link to login: <br /><br />
							<a href="http://www.'.$_SERVER['HTTP_HOST'].$this->app->path.'manage/">http://www.'.$_SERVER['HTTP_HOST'].$this->app->path.'manage/</a>'.'<br /><br />Once logged in you can manage your uplaods, upload new files and folders.<br /><br />Thanks, '.$this->app->settings['name'].' Team.';
			break;
			case 'notifyregister' :
				$subject = 'New User - ' . $this->app->settings['name'];
				$message = 'A new user has been registered as '.$args['name'];
			break;
		}
		$headers = "From: ".$this->app->settings['name']." Notification <noreply@".$_SERVER['HTTP_HOST'].">\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1";
		
		mail( $to, $subject, $message, $headers );
	}
//End functions 
}





?>
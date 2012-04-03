<?php

if( ! defined( '_AppPath' ) ) { exit( 'Direct access to this script is not permitted' ); }
 
class Thumb
{
 var $app;
 function __construct( $app )
 {
 	$this->app = $app;
 }
	// this is the function that will create the thumbnail image from the uploaded image
	// the resize will be done considering the width and height defined, but without deforming the image
	function make_thumb($img_name,$filename,$new_w,$new_h)
	{	
	 //get image dimensions
	 list($width, $height) = getimagesize($img_name);
	 
	 if(($width < $new_w) && ($height < $new_h))
	 {
	  //no resizing needed
	  return;
	 }
		
		//get image extension.
		$ext= $this->getExtension($img_name);
		//creates the new image using the appropriate function from gd library
		if(!strcmp("jpg",$ext) || !strcmp("jpeg",$ext))
		$src_img=imagecreatefromjpeg($img_name);
		
		if(!strcmp("png",$ext))
		$src_img=imagecreatefrompng($img_name);
		
		if(!strcmp("gif",$ext))
		$src_img=imagecreatefromgif($img_name);
		
		//gets the dimmensions of the image
		$old_x=imageSX($src_img);
		$old_y=imageSY($src_img);
	
	 //get difference
	 $dif = $old_x - $new_w;
	  
	 
	 //Check if image is bigger than 250px
	 //if it is we will resize the image
	 //if its in between 200 and 250 we will
	 // crop the image
	 if($dif < 350)
	 {  
	  $offset_x = 0;
			$offset_y = 0;
			
			$thumb_dim_w;
			$thumb_dim_h;
			
			// we create a new image with the new dimmensions
			if($old_y < $new_h )
				//generated resized image is smaller than thumbnail dimmensions
			 $thumb_dim_h = $old_y;
			else
			 $thumb_dim_h = $new_h;
			
			//compare resized width
			if($old_x < $new_w)
			 $thumb_dim_w = $old_x;
			else
			 $thumb_dim_w = $new_w;
			
			$dst_img=imagecreatetruecolor($thumb_dim_w,$thumb_dim_h);
			
	  // crop the big image to the new created one 
	  //save transparency if is png
	  if(!strcmp("png",$ext))
	  {
	   imagesavealpha($dst_img, true);
				imagealphablending($dst_img, false);
	  }
		 imagecopy($dst_img,$src_img,0,0,$offset_x,$offset_y,$old_x,$old_y); 
	 }
	 if($dif > 350)
	 {
	  //resize
	  // next we will calculate the new dimmensions for the thumbnail image
			// the next steps will be taken: 
			// 1. calculate the ratio by dividing the old dimmensions with the new ones
			//	 2. if the ratio for the width is higher, the width will remain the one define in WIDTH variable
			//	 and the height will be calculated so the image ratio will not change
			//	 3. otherwise we will use the height ratio for the image
			// as a result, only one of the dimmensions will be from the fixed ones	
			$ratio1=$old_x/$dif;
			$ratio2=$old_y/$new_h;
			if($ratio1>$ratio2)	{
			$thumb_w=$dif;
			$thumb_h=$old_y/$ratio1;
			}
			else	{
			$thumb_h=$new_h;
			$thumb_w=$old_x/$ratio2;
			}
			
			$thumb_dim_w;
			$thumb_dim_h;
			
			// we create a new image with the new dimmensions
			if($thumb_h < $new_h )
				//generated resized image is smaller than thumbnail dimmensions
			 $thumb_dim_h = $thumb_h;
			else
			 $thumb_dim_h = $new_h;
			
			//compare resized width
			if($thumb_w < $new_w)
			 $thumb_dim_w = $thumb_w;
			else
			 $thumb_dim_w = $new_w;
			
			$dst_img=imagecreatetruecolor($thumb_dim_w,$thumb_dim_h);
			
			//save transparency if is png
	  if(!strcmp("png",$ext))
	  {
	   imagesavealpha($dst_img, true);
				imagealphablending($dst_img, false);
	  }
			// resize the big image to the new created one
			imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
	 }
	
	
	
	// output the created image to the file. Now we will have the thumbnail into the file named by $filename
	if(!strcmp("png",$ext))
		imagepng($dst_img,$filename); 
	if(!strcmp("gif",$ext))
	 imagegif($dst_img,$filename);
	if(!strcmp("jpg",$ext) || !strcmp("jpeg",$ext))
		imagejpeg($dst_img,$filename,100); 
	
	//destroys source and destination images. 
	imagedestroy($dst_img); 
	imagedestroy($src_img);
	
	//Return sucess
	return "sucess";
	}

	// This function reads the extension of the file. 
	// It is used to determine if the file is an image by checking the extension. 
	function getExtension($str) {
	$i = strrpos($str,".");
	if (!$i) { return ""; }
	$l = strlen($str) - $i;
	$ext = substr($str,$i+1,$l);
	return strtolower($ext);
	}

}
?>
<?php
	die();
	$size			= ($_GET['size'] == 'small') ? 'small' : 'big';
	$file_name		= $_GET['file'];
	$file_watermark	= 'watermark.png';
	$path		= array(
						'small'		=> '/home/basketnews/domains/basketnews.lt/public_html/images/photos_small/',
						'big'		=> '/home/basketnews/domains/basketnews.lt/public_html/images/photos_big/',
						'watermark'	=> '/home/basketnews/domains/basketnews.lt/public_html/images/web/',
					);
	
	
	
	header('content-type: image/jpeg');
	//header('content-type: image/png');
	
	if ($size == 'big') {
		//$image = imagecreatetruecolor($watermark_width, $watermark_height);  
		//$image = imagecreatefromjpeg($path[$size] . $file_name);
		$img_size = getimagesize($path[$size] . $file_name);
		$origWidth = $img_size[0];
		$origHeight = $img_size[1];
		
		$watermark = imagecreatefrompng($path['watermark'] . $file_watermark);
		$watermark_width = imagesx($watermark);  
		$watermark_height = imagesy($watermark);
		$watermark_target = $path['big'] . 'watermark.tmp.png';
		
		$resizeWidth = ($origWidth < 300) ? ($origWidth * 0.9) : 200;
		$resizeHeight = ($origHeight < 300) ? ($origHeight * 0.9) : 200;
		
		resize_png_image($path['watermark'] . $file_watermark, $resizeWidth, $resizeHeight, $watermark_target);
	
		// get the size info for this watermark.
		$wmInfo = getimagesize($watermark_target);
		$waterMarkDestWidth = $wmInfo[0];
		$waterMarkDestHeight = $wmInfo[1];
		
		$differenceX = $origWidth - $waterMarkDestWidth;
		$differenceY = $origHeight - $waterMarkDestHeight;
		
		// where to place the watermark?
		$h_position = 'center';
		switch($h_position){
			// find the X coord for placement
			case 'left':
				$placementX = $edgePadding;
				break;
			case 'center':
				$placementX =  round($differenceX / 2);
				break;
			case 'right':
				$placementX = $origWidth - $waterMarkDestWidth - $edgePadding;
				break;
		}
		
		$v_position = 'center';
		switch($v_position){
			// find the Y coord for placement
			case 'top':
				$placementY = $edgePadding;
				break;
			case 'center':
				$placementY =  round($differenceY / 2);
				break;
			case 'bottom':
				$placementY = $origHeight - $waterMarkDestHeight - $edgePadding;
				break;
		}
		
		if ($img_size[2]==3)
			$resultImage = imagecreatefrompng($path[$size] . $file_name);
		else
			$resultImage = imagecreatefromjpeg($path[$size] . $file_name);
		imagealphablending($resultImage, TRUE);
		
		$finalWaterMarkImage = imagecreatefrompng($watermark_target);
		$finalWaterMarkWidth = imagesx($finalWaterMarkImage);
		$finalWaterMarkHeight = imagesy($finalWaterMarkImage);
		
		
		imagecopy($resultImage,
				  $finalWaterMarkImage,
				  $placementX,
				  $placementY,
				  0,
				  0,
				  $finalWaterMarkWidth,
				  $finalWaterMarkHeight
		);
		
		if($img_size[2]==3){
			imagealphablending($resultImage,FALSE);
			imagesavealpha($resultImage,TRUE);
			imagepng($resultImage,$target,$quality);
		}else{
			imagejpeg($resultImage, '', 100); 
		}
	
		imagedestroy($resultImage);
		imagedestroy($finalWaterMarkImage);
		
	} else {
		$resultImage = imagecreatefromjpeg($path[$size] . $file_name);
		imagejpeg($resultImage, '', 100); 
	}
	
	/*
	$image = imagecreatetruecolor($watermark_width, $watermark_height);  
	$image = imagecreatefromjpeg($path[$size] . $file_name);
	$img_size = getimagesize($path[$size] . $file_name);  
	$dest_x = $img_size[0] - $watermark_width - 5;  
	$dest_y = $img_size[1] - $watermark_height - 5;  
	imagecopymerge($image, $watermark, $dest_x, $dest_y, 0, 0, $watermark_width, $watermark_height, 100);  
	imagejpeg($image);  
	imagedestroy($image);  
	imagedestroy($watermark);  
	*/
	

	function resize_png_image($img,$newWidth,$newHeight,$target){
		$srcImage=imagecreatefrompng($img);
		if($srcImage==''){
			return FALSE;
		}
		$srcWidth=imagesx($srcImage);
		$srcHeight=imagesy($srcImage);
		$percentage=(double)$newWidth/$srcWidth;
		$destHeight=round($srcHeight*$percentage)+1;
		$destWidth=round($srcWidth*$percentage)+1;
		if($destHeight > $newHeight){
			// if the width produces a height bigger than we want, calculate based on height
			$percentage=(double)$newHeight/$srcHeight;
			$destHeight=round($srcHeight*$percentage)+1;
			$destWidth=round($srcWidth*$percentage)+1;
		}
		$destImage=imagecreatetruecolor($destWidth-1,$destHeight-1);
		if(!imagealphablending($destImage,FALSE)){
			return FALSE;
		}
		if(!imagesavealpha($destImage,TRUE)){
			return FALSE;
		}
		if(!imagecopyresampled($destImage,$srcImage,0,0,0,0,$destWidth,$destHeight,$srcWidth,$srcHeight)){
			return FALSE;
		}
		if(!imagepng($destImage,$target)){
			return FALSE;
		}
		imagedestroy($destImage);
		imagedestroy($srcImage);
		return TRUE;
	}
?>

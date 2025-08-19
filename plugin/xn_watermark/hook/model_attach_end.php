<?php exit;
function sg_watermark($img, $ext) {
	
		$sg_watermark = setting_get('xn_watermark');
		$QR = imagecreatefromstring(file_get_contents($img)); 
		$QR_width = imagesx($QR);
		$QR_height = imagesy($QR);
		
		if(!array_key_exists($ext, $sg_watermark['format'])) return;
		
		if($sg_watermark['type'] == '1') {
			
			$logo = imagecreatefromstring(file_get_contents('./plugin/xn_watermark/img/logo.png')); 
			$logo_width = imagesx($logo);
			$logo_height = imagesy($logo);
			
			if($sg_watermark['width'] == '1') {
				
				$logo_qr_width = $QR_width / 6; 
				$scale = $logo_width/$logo_qr_width; 
				$logo_qr_height = $logo_height/$scale;
				
			} else {
				
				$logo_qr_width = $logo_width; 
				$logo_qr_height = $logo_height;
				
			}
			
		} else {
			
			$temp = imagettfbbox($sg_watermark['size'],0,"./plugin/xn_watermark/font/$sg_watermark[font]",$sg_watermark['text']);
			$logo_width= $temp[2] - $temp[6];
			$logo_height= $temp[3] - $temp[7];
			 unset($temp);
			$logo_qr_width = $logo_width; 
			$logo_qr_height = $logo_height;
			
		}
		
		switch ($sg_watermark['position']) { 
		
			case 1:	$posX = 10;	$posY = 10;	break; 
			case 2:	$posX = ($QR_width - $logo_qr_width) / 2;	$posY = 10; break; 
			case 3:	$posX = $QR_width - $logo_qr_width - 10;	$posY = 10;	break; 
			case 4:	$posX = 10;	$posY = ($QR_height - $logo_qr_height) / 2;	break; 
			case 5:	$posX = ($QR_width - $logo_qr_width) / 2;	$posY = ($QR_height - $logo_qr_height) / 2;	break; 
			case 6:	$posX = $QR_width - $logo_qr_width - 10;	$posY = ($QR_height - $logo_qr_height) / 2;	break; 
			case 7:$posX = 10;$posY = $QR_height - $logo_qr_height-10;break; 
			case 8:	$posX = ($QR_width - $logo_qr_width) / 2;	$posY = $QR_height - $logo_qr_height-10;	break; 
			case 9:	$posX = $QR_width - $logo_qr_width - 10;	$posY = $QR_height - $logo_qr_height - 10;	break; 
			default:	$posX = rand(0, ($QR_width - $logo_qr_width));	$posY = rand(0, ($QR_height - $logo_qr_height));	break; 
			
		}
		
		if($sg_watermark['type'] == '1') {
			
			imagecopyresampled($QR, $logo, $posX, $posY, 0, 0, $logo_qr_width,$logo_qr_height, $logo_width, $logo_height);
			
		} else {
			
			$font = "./plugin/xn_watermark/font/$sg_watermark[font]";
			
			if( !empty($sg_watermark['color']) && (strlen($sg_watermark['color'])==7) ) {
				
				$R = hexdec(substr($sg_watermark['color'],1,2));
				$G = hexdec(substr($sg_watermark['color'],3,2));
				$B = hexdec(substr($sg_watermark['color'],5));
				
			}
			
			imagefttext($QR, $sg_watermark['size'], 0, $posX, $posY, imagecolorallocate($QR, $R, $G, $B), $font, $sg_watermark['text']);
			
		}
		
		switch($ext){
			
		  case 'gif':	imagegif($QR, $img);	break;
		  case 'png':	imagepng($QR, $img);	break;
		  default:	imagejpeg($QR, $img);	break;
		  
		}
		
		imagedestroy($QR);
		
}
?>
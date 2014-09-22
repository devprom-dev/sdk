<?php

class CssSpritesGenerator
{
	static function storeSpriteFile( $sprite_file_name, $image_files, $max_width, $max_height, $default_file )
	{
		$sprites_on_row = floor(32767 / $max_width);
		 
		$sprite_width = min($sprites_on_row, max(array_keys($image_files))) * $max_width;

		$sprite_height = max($max_height, floor(max(array_keys($image_files)) / $sprites_on_row) * $max_height);
		
		$im = imagecreatetruecolor($sprite_width, $sprite_height);
		
		imagesavealpha($im, true);
		
		$alpha = imagecolorallocatealpha($im, 0, 0, 0, 127);
		
		imagefill($im,0,0,$alpha);
 
		foreach( $image_files as $index => $file )
		{
			if ( !file_exists($file) ) continue;
			
			$im2 = imagecreatefrompng($file);
			
			if ( $im2 === false )
			{
				$im2 = imagecreatefromjpeg($file);
			}
			
			if ( $im2 === false )
			{
				$im2 = imagecreatefromgif($file);
			}

			if ( $im2 === false )
			{
				$im2 = imagecreatefromwbmp($file);
			}
			
			if ( $im2 === false )
			{
				$im2 = imagecreatefrompng($default_file);
				
				list($width, $height, $type, $attr) = getimagesize($default_file);
			}
			else
			{
				list($width, $height, $type, $attr) = getimagesize($file);
			}
			
			$row = floor($index / $sprites_on_row);
			
			$column = $index - $row * $sprites_on_row - 1;

			imagecopyresampled($im,$im2,($max_width*$column),($max_height*$row),0,0,$max_width,$max_height, $width, $height);
		}

		imagepng($im,$sprite_file_name);
		
		imagedestroy($im);
	}
}
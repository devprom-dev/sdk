<?php

function lumdiff($left,$right)
{
	list($R1,$G1,$B1) = $left;
	list($R2,$G2,$B2) = $right;
	 
    $L1 = 0.2126 * pow($R1/255, 2.2) +
          0.7152 * pow($G1/255, 2.2) +
          0.0722 * pow($B1/255, 2.2);
 
    $L2 = 0.2126 * pow($R2/255, 2.2) +
          0.7152 * pow($G2/255, 2.2) +
          0.0722 * pow($B2/255, 2.2);
 
    if($L1 > $L2){
        return ($L1+0.05) / ($L2+0.05);
    }else{
        return ($L2+0.05) / ($L1+0.05);
    }
}

function hex2rgb($hex) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   return array($r, $g, $b);
}

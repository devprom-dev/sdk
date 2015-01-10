<?php 

$text_rgb = array(255,255,255);
if ( $color == 'false' || $color == '' )
{
	$color_class = $terminal ? 'label-success' : 'label-warning';
}
else
{
	$background_rgb = hex2rgb(trim($color,'#'));
	
	if ( lumdiff($background_rgb, $text_rgb) < 2 )
	{
		$text_rgb = array(
				max($background_rgb[0] / 3,0),
				max($background_rgb[1] / 3,0),
				max($background_rgb[2] / 3,0)
		);
	}
	$style="text-shadow:none";
}
$text_color = '#'.str_pad(dechex($text_rgb[0]),2,"0").str_pad(dechex($text_rgb[1]),2,"0").str_pad(dechex($text_rgb[2]),2,"0");

?>
<span class="label <?=$color_class?>" id="<?=$id?>" style="background-color:<?=$color?>;color:<?=$text_color?>;<?=$style?>"><?=$name?></span>
<?php 

if ( $image == '' ) $image = 'userpics';

switch ( $image )
{
    case 'userpics':
    	$size = 45;
    	break;
	case 'userpics-middle':
		$size = 30;
		break;
	case 'userpics-mini':
		$size = 18;
		break;
}

$sprites_on_row = floor(32767 / $size);
$row = floor($id / $sprites_on_row);
$column = $id - $row * $sprites_on_row - 1;
$timestamp = getSession()->getUserPicTimestamp();

?>
<div class="<?=$class?>" title="<?=$title?>" style="background: url('/images/<?=$image?>.png?v=<?=$timestamp?>') no-repeat -<?=($column * $size)?>px <?=(max(0, -1 * $row * $size))?>px;"></div>
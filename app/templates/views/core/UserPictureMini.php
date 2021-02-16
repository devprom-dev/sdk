<?php 
$size = 18;
$sprites_on_row = floor(32767 / $size);
$row = floor($id / $sprites_on_row);
$column = $id - $row * $sprites_on_row - 1;
$timestamp = getSession()->getUserPicTimestamp();
?>
<span class="<?=$class?>" title="<?=$title?>" style="background: url('/images/userpics-mini.png?v=<?=$timestamp?>') no-repeat -<?=($column * $size)?>px <?=(max(0,-1 * $row * $size))?>px;"></span>
<?php
if ( $date != '' ) {
    echo ' &nbsp; ' . $date;
}

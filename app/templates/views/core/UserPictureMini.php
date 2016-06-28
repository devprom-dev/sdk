<?php 
$size = 18;
$sprites_on_row = floor(32767 / $size);
$row = floor($id / $sprites_on_row);
$column = $id - $row * $sprites_on_row - 1;
?>
<span class="<?=$class?>" title="<?=$title?>" style="background: url('/sprite/userpics-mini.png') no-repeat -<?=($column * $size)?>px -<?=($row * $size)?>px;"></span>
<?php 
$size = 18;
$sprites_on_row = floor(32767 / $size);
$row = floor($id / $sprites_on_row);
$column = $id - $row * $sprites_on_row - 1;
$timestamp = filemtime(SERVER_ROOT_PATH."images/userpics-mini.png");
?>
<span class="<?=$class?>" title="<?=$title?>" style="background: url('/images/userpics-mini.png?v=<?=$timestamp?>') no-repeat -<?=($column * $size)?>px -<?=($row * $size)?>px;"></span>
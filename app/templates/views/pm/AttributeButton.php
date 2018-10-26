<?php
$id = md5(uniqid(time().$random,true));
?>
<div class="btn-group">
	<a class="btn btn-light btn-field dropdown-toggle <?=$extraClass?>" data-toggle="dropdown" href="#" data-placement="right" data-target="#att-btn-<?=$id?>" title="<?=$title?>" tabindex="-1">
        <?=($data == '' ? '...' : $data)?>
	</a>
</div>
<div class="btn-group dropdown-fixed" id="att-btn-<?=$id?>">
	<? echo $this->render('core/PopupMenu.php', array ( 'items' => $items ));?>
</div>

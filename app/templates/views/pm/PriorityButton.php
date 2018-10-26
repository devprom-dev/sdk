<?php
$id = md5(uniqid(time().$random,true));
?>

<div class="btn-group">
    <span class="btn btn-priority btn-field">
        <span style="color:<?=$color?>">⚫</span>
    </span>
	<a class="btn btn-light btn-field dropdown-toggle" data-toggle="dropdown" href="#" data-placement="right" data-target="#priority-button-<?=$id?>">
        <?=$data?>
	</a>
</div>
<div class="btn-group dropdown-fixed" id="priority-button-<?=$id?>">
	<? echo $this->render('core/PopupMenu.php', array ( 'items' => $items ));?>
</div>

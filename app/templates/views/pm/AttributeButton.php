<div class="btn-group">
	<a id="<?=$id?>" class="btn btn-light btn-field dropdown-toggle <?=$extraClass?>" data-toggle="dropdown" title="<?=$title?>" tabindex="-1">
        <?=($data == '' ? '...' : $data)?>
	</a>
    <? echo $this->render('core/PopupMenu.php', array ( 'items' => $items ));?>
</div>

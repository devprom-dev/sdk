<div class="btn-group">&nbsp;</div>
<div class="btn-group">&nbsp;</div>
<?php foreach( $actions as $item ) { ?>
<div class="btn-group">
	<a id="<?=$item['uid']?>" class="btn btn-xs btn-success" href="<?=$item['url']?>">
   		<i class="icon-plus icon-white"></i> <?=$item['name']?>
   	</a>
</div>
<?php } ?>

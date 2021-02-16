<?php
$id = md5(uniqid(time().$random,true));
?>
<div class="btn-group" style="text-align:left;" title="<?=$title?>">
	<a class="dropdown-toggle" data-toggle="dropdown" href="" data-placement="right" uid="estimation">
		<span class="label <?=($data != '0' ? 'label-success': "")?>">
			<?=$data?>
		</span>
        <? echo $this->render('core/PopupMenu.php', array ( 'items' => $items ));?>
	</a>
</div>

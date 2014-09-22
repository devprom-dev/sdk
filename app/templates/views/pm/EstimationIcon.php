<div class="btn-group" style="margin:0;height:18px;text-align:left;" title="<?=$title?>">
	<a class="dropdown-toggle" data-toggle="dropdown" href="#" data-placement="right">
		<span class="label <?=($data > 0 ? 'label-success': "")?>" data-toggle="context" data-target="#context-menu-estimation">
			<?=$data?>
		</span>
	</a>
	<? echo $this->render('core/PopupMenu.php', array ( 'items' => $items ));?>
</div>

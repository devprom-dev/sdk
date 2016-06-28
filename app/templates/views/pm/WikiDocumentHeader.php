<div class="hidden-print hover-holder" style="display:table;width:100%;">
	<div class="filter" style="display:table-cell;font-weight:normal;width:60%;vertical-align:top;">
		<?php foreach( $actions as $action ) { ?>
			<?php if ( count($action['items']) < 1 ) continue; ?>
		
			<div class="btn-group pull-left" style="padding-right: 5px;">
			   	<a uid="<?=$action['uid']?>" class="btn dropdown-toggle btn-small <?=($action['class'] == '' ? 'btn-inverse' : $action['class'])?>" href="#" data-toggle="dropdown">
			   		<?=$action['name']?>
			   		<span class="caret"></span>
			   	</a>
			   	<? echo $view->render('core/PopupMenu.php', array ('items' => $action['items'])); ?>
			</div>
		<?php } ?>

		<?php if ( $baselines_widget['name'] != '' ) { ?>
			<a href="<?=$baselines_widget['url']?>" class="dashed dashed-hidden" style="margin-top:3px;" target="_blank"><?=$baselines_widget['name']?></a>
		<?php } ?>
	</div>
	
	<div style="display:table-cell;width:40%;font-weight:normal;text-align: right;vertical-align:top;">
		<?php echo count($documents) > 0 ? $view->render('pm/WikiCompareButtons.php', array('documents' => $documents)) : ''; ?>
	</div>
</div>
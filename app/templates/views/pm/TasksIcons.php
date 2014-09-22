<?php

foreach ( $states as $key => $state )
{
	if ( !is_array($state) ) continue;
	
	switch ( $state['progress'] )
	{
		case '100%':
			$states[$key]['class'] = 'label-success';
			break;

		case '0%':
			$states[$key]['class'] = 'label-important';
			break;
	}
}

?>

<?php foreach ( $states as $key => $state ) { ?>

<div class="btn-group" style="margin:0;height:<?=($state['photo_id'] != '' ? '21': '18')?>px;">

	<?php if ( $state['url'] != '' ) { ?>
	<a class="with-tooltip dropdown-toggle" data-toggle="dropdown" href="#" data-placement="right" data-original-title="" data-content="" info="<?=$state['url']?>">
	<?php } ?>
		
		<? if ( $state['photo_id'] != '' ) { ?>
		
			<? echo $view->render('core/UserPicture.php', array ( 'id' => $state['photo_id'], 'class' => 'user-mini', 'image' => 'userpics-mini', 'title' => $state['photo_title'] )); ?>
		
		<? } else { ?>
		
		<span class="label <?=$state['class']?>" data-toggle="context" data-target="#context-menu-<?=$state['id']?>">
			<?=$state['name']?>
		</span>
		
		<? } ?>

	<?php if ( $state['url'] != '' ) { ?>
	</a>
	<?php } ?>
	
	<? echo is_array($state['actions']) ? $this->render('core/PopupMenu.php', array ( 'items' => $state['actions'] )) : ''; ?>

</div>

<?php } ?>

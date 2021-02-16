<?php

foreach ( $states as $key => $state ) {
	if ( !is_array($state) ) continue;
	switch ( $state['progress'] ) {
		case '100%':
			$states[$key]['class'] = 'label-success';
			break;
		case '0%':
			$states[$key]['class'] = 'label-important';
			break;
	}
}

foreach ( $states as $key => $state ) {
	$id = md5(uniqid(time().$key.$random,true));

?>
	<div class="btn-group board-tasks">
		<?php if ( $state['url'] != '' ) { ?>
		<a class="with-tooltip dropdown-toggle" data-toggle="dropdown" data-target="#tasksicons<?=$id?>" href="" data-placement="right" data-original-title="" data-content="" info="<?=$state['url']?>">
		<?php } ?>

			<? if ( $state['photo_id'] != '' && $state['class'] != 'label-success' ) { ?>

				<? echo $view->render('core/UserPicture.php', array ( 'id' => $state['photo_id'], 'class' => 'user-mini', 'image' => 'userpics-mini', 'title' => $state['photo_title'] )); ?>

			<? } else { ?>

			<span class="label <?=$state['class']?>" data-toggle="context" data-target="#context-menu-<?=$state['id']?>">
				<?=($state['name'] != '' ? $state['name'] : "T")?>
			</span>

			<? } ?>

		<?php if ( $state['url'] != '' ) { ?>
		</a>
		<?php } ?>
        <?php if ( is_array($state['actions']) && count($state['actions']) > 0 ) { ?>
            <div class="btn-group board-tasks dropdown-fixed" id="tasksicons<?=$id?>">
                <? echo $this->render('core/PopupMenu.php', array ( 'items' => $state['actions'] ));?>
            </div>
        <? } ?>
	</div>
<?php } ?>

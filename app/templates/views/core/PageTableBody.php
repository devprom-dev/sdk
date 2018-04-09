<?
$detailsVisible = false;
$detailsUsed = false;
if ( is_array($sections) ) {
	foreach( $sections as $section ) {
		if ( $section instanceof DetailsInfoSection ) {
            $detailsUsed = true;
			$detailsVisible = $section->isActive();
		}
	}
}
?>

<? if ( !$tableonly ) { ?>

<div class="table-header">
<div class="filter hidden-print">

<?php if ( $title != '' ) { ?>

<ul class="breadcrumb hidden-print">
    <?php if ( $navigation_title != '' && $navigation_title != $title ) { ?>
	<li>
	    <a href="<?=$navigation_url?>"><?=$navigation_title?></a>
	    <span class="divider">/</span>
	</li>
	<?php } ?>
    <li>
	    <?=$title?>
	</li>
</ul> <!-- end breadcrumb -->

<?php } ?>

<?php

	$filter_actions = $table->getFilterActions();
	if ( count($filter_actions) > 0 || count($filter_items) > 0 ) {
        if (count($filter_actions) > 0) { ?>

            <div class="btn-group pull-left" style="margin-right:5px;display:table;">
                <a id="filter-settings" class="btn dropdown-toggle btn-small" data-toggle="dropdown" href="#">
                    <i class="icon-cog icon-gray"></i>
                    <?php if ($filter_actions[0]['name'] != '') { ?>
                        <span class="caret"></span>
                    <?php } ?>
                </a>
                <?php

                echo $view->render('core/PopupMenu.php', array(
                    'items' => $filter_actions
                ));
                ?>
            </div>

            <?php

        }

        foreach ($filter_items as $filter) {
            if ($filter['html'] != '') {
                echo '<div class="btn-group pull-left">' . $filter['html'] . '</div>';
            } else {
                ?>
                <div class="btn-group pull-left">
                    <a class="btn btn-small dropdown-toggle <?= (in_array($filter['value'], array('', 'all')) ? '' : 'btn-info') ?>"
                       uid="<?= $filter['name'] ?>" href="#" data-toggle="dropdown">
                        <?= $filter['title'] ?>
                        <span class="caret"></span>
                    </a>
                    <? echo $view->render('core/PopupMenu.php', array('items' => $filter['actions'], 'uid' => $filter['name'])); ?>
                </div>
                <?php
            }
        }

        if (count($filter_items) > 0 && count($filterMoreActions) > 0) { ?>
            <div class="btn-group pull-left">
                <a class="btn btn-cell dropdown-toggle transparent-btn btn-filter-more" uid="filter-more-actions" href="#" data-toggle="dropdown">
                    <span class="label">...</span>
                </a>
                <? echo $view->render('core/PopupMenu.php', array('items' => $filterMoreActions, 'uid' => 'filter-more-actions')); ?>
            </div>
        <?php
        }
    }

if ( !$tableonly && is_object($list) && !is_a($list, 'PageChart') ) { ?>
	<div class="bulk-filter-actions pull-left">
		<?php foreach( $bulk_actions['workflow'] as $stateRefName => $workflow_actions ) { ?>
			<div class="btn-group pull-left" object-state="<?=$stateRefName?>">
				<a class="btn dropdown-toggle btn-small btn-warning" href="#" data-toggle="dropdown">
					<i class="icon-hand-right icon-white"></i>
					<?=translate("Состояние")?>
					<span class="caret"></span>
				</a>
				<? echo $view->render('core/PopupMenu.php', array ('items' => $workflow_actions)); ?>
			</div>
		<?php } ?>
		<?php if( count($bulk_actions['modify']) > 0 ) { ?>
			<div id="bulk-modify-actions" class="btn-group pull-left">
				<a class="btn dropdown-toggle btn-small" href="#" data-toggle="dropdown" title="<?=translate('Изменить')?>">
					<i class="icon-pencil"></i>
					<span class="caret"></span>
				</a>
				<? echo $view->render('core/PopupMenu.php', array ('items' => $bulk_actions['modify'])); ?>
			</div>
		<?php } ?>
		<?php if( count($bulk_actions['action']) > 0 ) { ?>
			<div id="bulk-actions" class="btn-group pull-left">
				<a class="btn dropdown-toggle btn-small" href="#" data-toggle="dropdown" >
					<?=translate('Ещё...')?>
					<span class="caret"></span>
				</a>
				<? echo $view->render('core/PopupMenu.php', array ('items' => $bulk_actions['action'])); ?>
			</div>
		<?php } ?>
		<?php foreach( $bulk_actions['delete'] as $item ) { ?>
			<div class="btn-group pull-left">
				<a id="<?=$item['uid']?>" class="btn btn-small btn-danger" href="<?=$item['url']?>">
					<?=$item['name']?>
				</a>
			</div>
		<?php } ?>
	</div>
	<?php
}
?>
</div> <!-- end filter -->

<div class="hidden-print filter-actions">
	<? if ( $list instanceof PageBoard ) { ?>
	<span id="board-slider"></span>
	<? } else if ( $list_slider ) { ?>
        <span id="list-slider"></span>
    <? } ?>

<?php if ( !$tableonly ) { ?>
	<?php foreach( $additional_actions as $action ) { ?>
        <?php $buttonsNumber = count(array_filter($action['items'], function($item){return $item['name'] != '';})); ?>
		<?php if ( $buttonsNumber < 5 ) { ?>
			<?php foreach( $action['items'] as $key => $item ) { ?>
				<?php if ( $item['name'] == '' ) continue; ?>
				<div class="btn-group pull-left plus-action">
					<a id="<?=($item['uid'] != '' ? $item['uid'] : $key)?>" class="btn append-btn btn-small <?=($item['class'] == '' ? 'btn-success' : $item['class'])?>" href="<?=$item['url']?>">
						<i class="icon-plus icon-white"></i> <?=($buttonsNumber > 2 ? TextUtils::getWords($item['name']) : $item['name'])?>
					</a>
				</div>
			<?php } ?>
		<?php } else { ?>
			<div class="btn-group pull-left plus-action">
				<a class="btn dropdown-toggle btn-small <?=($action['class'] == '' ? 'btn-success' : $action['class'])?>" href="#" data-toggle="dropdown">
					<?=$action['name']?>
					<span class="caret"></span>
				</a>
				<? echo $view->render('core/PopupMenu.php', array ('items' => $action['items'])); ?>
			</div>
		<?php } ?>
	<?php } ?>

	<?php echo $view->render('core/PageSectionButtons.php', array (
		'sections' => $sections,
		'object_class' => $object_class,
		'object_id' => $object_id,
		'iterator' => $list->getIteratorRef(),
		'table_id' => $widget_id
	));
	?>

	<?php if ( count($actions) > 0 ) { ?>
		<div class="btn-group last pull-left">
			<a class="btn dropdown-toggle btn-small btn-inverse" href="#" data-toggle="dropdown">
				<?=translate('Действия')?>
				<span class="caret"></span>
			</a>
			<? echo $view->render('core/PopupMenu.php', array ('items' => $actions)); ?>
		</div>
	<?php } ?>
<?php } ?>

</div> <!-- end actions -->

</div> <!-- table-header -->

<?php
}
?>
<div class="clearfix"></div>

<?php if ( $filter_actions[0]['name'] != '' && $filter_modified ) { ?>

<div class="hidden-print alert alert-filter alert-warning">
	<button type="button" class="close" data-dismiss="alert">
		<span style="font-size:12px;vertical-align:top"><?=translate('закрыть')?></span> &times;
	</button>
	<?php
        $items = $filter_actions['view-settings']['items'];
        $persistItem = array_shift($items);

        $html = $view->render('core/RowGroupMenu.php', array (
            'title' => '<a href="'.$persistItem['url'].'">'.$persistItem['name'].'</a> ',
            'items' => $items,
            'id' => 'settings-persist-alert'
        ));
        echo str_replace('%1', $html, text(1318));
    ?>
</div>

<?php } ?>

<div id="tablePlaceholder" class="<?=$placeholderClass?> <?=$sliderClass?>">
	<?php if ( is_array($changed_ids) ) foreach ( $changed_ids as $id ) { ?>
		<div class="object-changed" object-id="<?=$id?>"></div>
	<?php } ?>

	<div class="table-master">
    <?php
		$view->addGlobal('widget_id', $widget_id);
     	is_object($list)
    		? $list->render( $view, array(
    					'object_id' => $object_id,
    					'object_class' => $object_class,
						'title' => $title,
						'widget_id' => $widget_id,
						'tableonly' => $tableonly
    				)) 
    		: $table->draw( $view );

		$is_need_navigator = $table->IsNeedNavigator() && is_object($list) && $list->moreThanOnePage();
		if ( is_object($list) && $is_need_navigator ) $list->drawNavigator(false); else $table->drawFooter();

		echo '<div class="hint-holder">';
			echo $view->render('core/Hint.php', array('title' => $hint, 'name' => $page_uid, 'open' => $hint_open));
		echo '</div>';
	?>
	</div>
	<? if ( $detailsUsed && !is_a($list, 'PageChart') && !$tableonly && count($details) > 0 ) { ?>
		<div class="table-details" default="<?=($detailsVisible ? 'table-cell' : 'none')?>" style="height:100%;display:none;">
			<?php
				$list->getIteratorRef()->moveFirst();
				echo $view->render('core/PageTableDetails.php', array (
					'details' => $details,
					'details_id' => $widget_id,
					'visible' => $detailsVisible,
					'details_parms' => $details_parms,
					'default_id' => $list->getIteratorRef()->getId()
				));
			?>
		</div>
	<? } ?>
</div>
<div id="documentCache">
</div>

<?php

if ( !$tableonly ) {
	$table->drawScripts();
}
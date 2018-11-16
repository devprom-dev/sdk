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
$filter_actions = $table->getFilterActions();

$rowsActions = $filter_actions['rows']['items'];
unset($filter_actions['rows']);

$filtersMenu = $filter_actions['filters'];
if ( is_array($filtersMenu) ) {
    array_unshift($filterMoreActions, array());
    $filterMoreActions = array_merge(
            array( 'filters' => $filtersMenu ),
            $filterMoreActions
        );
    unset($filter_actions['filters']);
}
?>

<? if ( !$tableonly ) { ?>


<?php if ( $title != '' ) { ?>

<ul class="breadcrumb hidden-print">
    <?php
    if ( $navigation_url != '' && $nearest_title != '' ) {
        echo '<li><a href="'.$navigation_url.'">'.$nearest_title.'</a><span class="divider">/</span></li>';
    }
    ?>
	<li class="page-title">
        <div class="btn-group">
            <div class="btn transparent-btn">
                <span class="title"><?=$title?></span>
            </div>
        </div>
	</li>
    <?php
        $family = $filter_actions['modules'];
        if ( is_array($family) ) {
            echo '<li>';
                echo $view->render('core/TextMenu.php',
                    array(
                        'title' => $family['name'],
                        'items' => $family['items']
                    )
                );
            echo '</li>';
            unset($filter_actions['modules']);
        }

        $charts = $filter_actions['charts'];
        if ( is_array($charts) ) {
            echo '<li>';
            echo $view->render('core/TextMenu.php',
                array(
                    'title' => $charts['name'],
                    'items' => $charts['items']
                )
            );
            echo '</li>';
            unset($filter_actions['charts']);
        }

        $openProjects = $filter_actions['projects'];
        if ( is_array($openProjects) ) {
            echo '<li>';
            echo $view->render('core/TextMenu.php',
                array(
                    'title' => $openProjects['name'],
                    'items' => $openProjects['items']
                )
            );
            echo '</li>';
            unset($filter_actions['projects']);
        }

        $moduleActions = $filter_actions['actions'];
        if ( is_array($moduleActions) ) {
            echo '<li>';
            ?>
            <div class="btn-group">
                <a id="<?=$moduleActions['uid']?>" class="btn transparent-btn btn-sm dropdown-toggle" href="<?=$moduleActions['url']?>" title="<?=$moduleActions['name']?>" style="padding-top:0;">
                    <i class="icon-star-empty"></i>
                </a>
            </div>
            <?php
            echo '</li>';
            unset($filter_actions['actions']);
        }
    ?>
</ul> <!-- end breadcrumb -->

<?php } ?>

<div class="table-header">

<?php
	if ( count($filter_items) > 0 )
	{
	    echo '<div class="filter hidden-print">';

        foreach ($filter_items as $filter) {
            if ($filter['html'] != '') {
                echo '<div class="btn-group pull-left">' . $filter['html'] . '</div>';
            } else {
                ?>
                <div class="btn-group pull-left">
                    <a class="btn btn-sm dropdown-toggle <?= (in_array($filter['value'], array('', 'all')) ? 'btn-light' : 'btn-info') ?>"
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

        echo '</div>';
    }

if ( !$tableonly && is_object($list) && !is_a($list, 'PageChart') && count($bulk_actions) > 0 ) { ?>
	<div class="bulk-filter-actions pull-left">
		<?php foreach( $bulk_actions['workflow'] as $stateRefName => $workflow_actions ) { ?>
			<div class="btn-group pull-left" object-state="<?=$stateRefName?>">
				<a class="btn dropdown-toggle btn-sm btn-warning" href="#" data-toggle="dropdown">
					<i class="icon-hand-right"></i>
					<?=translate("Состояние")?>
					<span class="caret"></span>
				</a>
				<? echo $view->render('core/PopupMenu.php', array ('items' => $workflow_actions)); ?>
			</div>
		<?php } ?>
		<?php if( count($bulk_actions['modify']) > 0 ) { ?>
			<div id="bulk-modify-actions" class="btn-group pull-left">
				<a class="btn dropdown-toggle btn-sm btn-light" href="#" data-toggle="dropdown" title="<?=translate('Изменить')?>">
					<i class="icon-pencil"></i>
					<span class="caret"></span>
				</a>
				<? echo $view->render('core/PopupMenu.php', array ('items' => $bulk_actions['modify'])); ?>
			</div>
		<?php } ?>
		<?php if( count($bulk_actions['action']) > 0 ) { ?>
			<div id="bulk-actions" class="btn-group pull-left">
				<a class="btn dropdown-toggle btn-sm btn-light" href="#" data-toggle="dropdown" >
					<?=translate('Ещё...')?>
					<span class="caret"></span>
				</a>
				<? echo $view->render('core/PopupMenu.php', array ('items' => $bulk_actions['action'])); ?>
			</div>
		<?php } ?>
		<?php foreach( $bulk_actions['delete'] as $item ) { ?>
			<div class="btn-group pull-left">
				<a id="<?=$item['uid']?>" class="btn btn-sm btn-danger" href="<?=$item['url']?>">
					<?=$item['name']?>
				</a>
			</div>
		<?php } ?>
	</div>
            <?php
	}
?>

<div class="hidden-print filter-actions">
	<? if ( $list instanceof PageBoard ) { ?>
	<span id="board-slider"></span>
	<? } else if ( $list_slider ) { ?>
        <span id="list-slider" title="<?=text(2648)?>"></span>
    <? } ?>

<?php if ( !$tableonly ) { ?>
	<?php foreach( $additional_actions as $action ) { ?>
        <?php
        $buttons = array_filter($action['items'], function($item){return $item['name'] != '';});

        foreach( array_slice($buttons, 0, 3) as $key => $item ) { ?>
            <div class="btn-group pull-left plus-action">
                <a id="<?=($item['uid'] != '' ? $item['uid'] : $key)?>" class="btn append-btn btn-sm <?=($item['class'] == '' ? 'btn-success' : $item['class'])?>" href="<?=$item['url']?>">
                    <i class="icon-plus icon-white"></i> <?=($buttonsNumber > 2 ? TextUtils::getWords($item['name']) : $item['name'])?>
                </a>
            </div>
        <?php
        }
        $restActions = array_slice($buttons, 3);
        if ( count($restActions) > 0 ) { ?>
			<div class="btn-group pull-left plus-action">
				<a class="btn dropdown-toggle btn-sm <?=($action['class'] == '' ? 'btn-success' : $action['class'])?>" href="#" data-toggle="dropdown">
					<?=$action['name']?>
					<span class="caret"></span>
				</a>
				<? echo $view->render('core/PopupMenu.php', array ('items' => $restActions)); ?>
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
			<a class="btn dropdown-toggle btn-secondary btn-sm" href="#" data-toggle="dropdown">
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

<?php if ( $filter_modified ) { ?>

<div class="hidden-print alert alert-filter alert-warning">
	<button type="button" class="close" data-dismiss="alert">
		<span style="font-size:12px;vertical-align:top"><?=translate('закрыть')?></span> &times;
	</button>
	<?php
        $items = $filterMoreActions;
        $persistItem = $items['personal-persist'];
        unset($items['personal-persist']);
        unset($items['filters']);
        unset($items['share']);
        unset($items['reset']);

        $html = $view->render('core/LinkMenu.php', array (
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
						'tableonly' => $tableonly,
                        'filter_actions' => $filter_actions
    				)) 
    		: $table->draw( $view );

		$is_need_navigator = $table->IsNeedNavigator() && is_object($list);

		if ( is_object($list) && $is_need_navigator ) {
		    $list->drawNavigator(false, $table->getRowsOnPage(), $rowsActions);
        } else {
		    $table->drawFooter();
        }

		echo '<div class="hint-holder">';
			echo $view->render('core/Hint.php', array('title' => $hint, 'name' => $page_uid, 'open' => $hint_open));
		echo '</div>';
	?>
	</div>
	<? if ( $detailsUsed && !is_a($list, 'PageChart') && !$tableonly && count($details) > 0 ) { ?>
		<div class="table-details" default="<?=($detailsVisible ? 'table-cell' : 'none')?>" style="height:100%;display:none;" >
			<?php
				$list->getIteratorRef()->moveFirst();
				echo $view->render('core/PageTableDetails.php', array (
					'details' => $details,
					'details_id' => $widget_id,
					'visible' => $detailsVisible,
					'details_parms' => $details_parms,
					'default_id' => $list->getIteratorRef()->getId(),
                    'className' => $object_class
				));
			?>
		</div>
	<? } ?>
</div>

<?php

if ( !$tableonly ) {
	$table->drawScripts();
}
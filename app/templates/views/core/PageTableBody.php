<?php
$filter_actions = !$tableonly ? $table->getFilterActions() : array();

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

<?php
    if ( $title != '' ) {
        echo $view->render('core/PageTableBreadcrumb.php', array(
            'navigation_url' => $navigation_url,
            'nearest_title' => $nearest_title,
            'title' => $title,
            'filter_actions' => $filter_actions
        ));
    }
?>

<div class="table-header <?=$placeholderClass?> hidden-print">

<?php
    if ( $filter_visible || $filter_search['searchable']) {
        echo $view->render('core/PageTableFilter.php', array(
            'filter_modified' => $filter_modified,
            'filter_buttons' => $filter_buttons,
            'filter_search' => $filter_search,
            'filterMoreActions' => $filterMoreActions,
            'filter_settings' => $filter_settings
        ));
    }

    if ( is_object($list) && !is_a($list, 'PageChart') && count($bulk_actions) > 0 ) { ?>

    <div class="bulk-filter-actions filter-cell pull-left">


		<?php foreach( $bulk_actions['workflow'] as $stateRefName => $workflow_actions ) { ?>
			<div class="btn-group pull-left" object-state="<?=$stateRefName?>">
				<a class="btn dropdown-toggle btn-sm btn-warning" data-toggle="dropdown">
					<i class="icon-hand-right"></i>
					<?=translate("Состояние")?>
					<span class="caret"></span>
				</a>
				<? echo $view->render('core/PopupMenu.php', array ('items' => $workflow_actions)); ?>
			</div>
		<?php } ?>
		<?php if( count($bulk_actions['modify']) > 0 ) { ?>
			<div id="bulk-modify-actions" class="btn-group pull-left">
				<a class="btn dropdown-toggle btn-sm btn-light" data-toggle="dropdown" title="<?=translate('Изменить')?>">
					<i class="icon-pencil"></i>
					<span class="caret"></span>
				</a>
				<? echo $view->render('core/PopupMenu.php', array ('items' => $bulk_actions['modify'])); ?>
			</div>
		<?php } ?>
		<?php if( count($bulk_actions['action']) > 0 ) { ?>
			<div id="bulk-actions" class="btn-group pull-left">
				<a class="btn dropdown-toggle btn-sm btn-light" data-toggle="dropdown" >
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

<?php } ?>

<div class="hidden-print filter-cell filter-actions">
    <table class="<?=$filter_visible ? 'pull-right' : ''?>">
        <tr>
            <td>
                <div class="action-buttons">
                    <? if ( $list instanceof PageBoard ) { ?>
                        <div class="btn-group pull-left" style="margin-left: 8px;">
                            <span id="board-slider"></span>
                        </div>
                    <? } else if ( $list_slider ) { ?>
                        <div class="btn-group pull-left" style="margin-left: 8px;padding-bottom: 6px;">
                            <span id="list-slider" title="<?=text(2648)?>"></span>
                        </div>
                    <? } ?>

                    <?php
                        $action = array_pop($additional_actions);
                        $buttons = array_filter($action['items'], function($item){return $item['name'] != '';});
                        $maxButtons = defined('MAX_PLUS_ACTIONS') ? MAX_PLUS_ACTIONS : 7;

                        foreach( array_slice($buttons, 0, $maxButtons) as $key => $item ) { ?>
                            <? if ( is_array($item['items']) ) { ?>
                            <div class="btn-group pull-left plus-action">
                                <a class="btn dropdown-toggle btn-sm <?=($item['class'] == '' ? 'btn-success' : $item['class'])?>" data-toggle="dropdown">
                                    <i class="<?=($item['icon'] != '' ? $item['icon'] : 'icon-plus')?>"></i> <?=$item['name']?>
                                    <span class="caret"></span>
                                </a>
                                <? echo $view->render('core/PopupMenu.php', array ('items' => $item['items'])); ?>
                            </div>
                            <? } else { ?>
                            <div class="btn-group pull-left plus-action">
                                <a id="<?=($item['uid'] != '' ? $item['uid'] : $key)?>" class="btn plus-action append-btn btn-sm <?=($item['class'] == '' ? 'btn-success' : $item['class'])?>" href="<?=$item['url']?>">
                                    <i class="<?=($item['icon'] != '' ? $item['icon'] : 'icon-plus')?> icon-white"></i> <?=($buttonsNumber > 2 ? TextUtils::getWords($item['name']) : $item['name'])?>
                                </a>
                            </div>
                            <? } ?>
                        <?php }
                        $restActions = array_slice($buttons, $maxButtons);
                        ?>
                </div>
            </td>
            <? if ( count($restActions) > 0 ) { ?>
            <td width="1">
                <div class="btn-group last">
                    <a class="btn dropdown-toggle btn-sm <?=($action['class'] == '' ? 'btn-success' : $action['class'])?>" data-toggle="dropdown">
                        <i class="icon-plus icon-white"></i>
                        <span class="caret"></span>
                    </a>
                    <? echo $view->render('core/PopupMenu.php', array ('items' => $restActions)); ?>
                </div>
            </td>
            <? } ?>
            <td width="1">
                <?php if ( count($actions) > 0 ) { ?>
                    <div class="btn-group last">
                        <a class="btn dropdown-toggle btn-secondary btn-sm" data-toggle="dropdown">
                            <?=translate('Действия')?>
                            <span class="caret"></span>
                        </a>
                        <? echo $view->render('core/PopupMenu.php', array ('items' => $actions)); ?>
                    </div>
                <?php } ?>
            </td>
        </tr>
    </table>
</div> <!-- end actions -->

</div> <!-- table-header -->

<?php } ?>

<div id="tablePlaceholder" class="<?=$placeholderClass?> <?=$sliderClass?>">
	<?php if ( is_array($changed_ids) ) foreach ( $changed_ids as $id ) { ?>
		<div class="object-changed" object-id="<?=$id?>" object-class="<?=$object_class?>"></div>
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

		if ( is_object($list) && $list->IsNeedNavigator() ) {
		    $list->drawNavigator($view, false, $table->getRowsOnPage());
        } else {
		    $table->drawFooter();
        }

        if ( $hint != '' ) {
            echo '<div class="hint-holder hidden-print">';
            echo $view->render('core/Hint.php', array('title' => $hint, 'name' => $page_uid, 'open' => $hint_open));
            echo '</div>';
        }
	?>
	</div>
	<? if ( $list->getDetailsPaneVisible() && !$tableonly && count($details) > 0 ) { ?>
		<div class="table-details invisible" style="height:100%;" onclick="if($(this).is('.invisible')){toggleMasterDetails(true);}">
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

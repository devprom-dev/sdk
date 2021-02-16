<?php 

$buttons = array();

?>

<div class="page-title" style="display:table;width:100%;">

<?php if ( $attributes['SectionNumber']['visible'] && $attributes['SectionNumber']['value'] != '' ) { ?>

<h4 class="title-cell bs" style="padding-left:0;">
	<div class="sec-num">
    	<?=$attributes['SectionNumber']['value']?>
	</div>
</h4>

<?php } ?>

    <?php if ( $persisted && $uid_icon != '' ) { ?>
        <div class="title-cell">
            <? echo $view->render('core/ClipboardButton.php', array ('url' => $uid_url, 'uid' => $uid)); ?>
        </div>
    <?php } ?>

    <h4 class="title-cell bs" style="width:auto;padding-left:0;">
    <? $attributes['Caption']['field']->draw(); ?>
</h4>

<div class="title-cell hidden-print" style="width:2%;"></div>

<? if ( count($compare_actions) > 0 ) { ?>
    <div class="title-cell hidden-print">
        <div class="btn-group operation last">
            <a tabindex="-1" uid="compare-actions" class="btn btn-xs btn-danger dropdown-toggle actions-button" data-toggle="dropdown" href="">
                <?=text(1725)?>
                <span class="caret"></span>
            </a>
            <?php
                echo $view->render('core/PopupMenu.php', array (
                    'items' => $compare_actions,
                    'uid' => 'compare-actions'
                ));
            ?>
        </div>
    </div>
<? } ?>

<?php if ( $revision['id'] != '' ) { ?>

<div class="title-cell hidden-print">
	<span class="label label-inverse">
		<?=translate('Изменение').': '.$revision['id']?>
	</span>
</div>

<?php } ?>

    <div class="title-cell hidden-print" style="padding-top:1px;">
        <?php

        echo $view->render('pm/CommentsIconDocument.php', array (
            'object_it' => $form->getSectionIt()
        ));

        ?>
    </div>

    <?php if ( $attributes['Tags']['visible'] && is_object($attributes['Tags']['field']) && $form->getSectionIt()->get('Tags') != '' ) { ?>
	<div class="title-cell hidden-print" style="padding-top:2px;">
		<?
		$attributes['Tags']['field']->setReadonly(true);
		$attributes['Tags']['field']->render($view);
		?>
	</div>
<?php } ?>

	<?php if ( $importanceColor != '' ) { ?>
		<div class="title-cell hidden-print" style="white-space:nowrap;">
			<span class="label label-importance" title="<?=$importanceText?>" style="background-color: <?=$importanceColor?>;">&nbsp; &nbsp;</span>
		</div>
	<?php } ?>

    <?php if ( $persisted && $attributes['PageType']['visible'] && is_object($attributes['PageType']['field']) ) { ?>
        <div class="title-cell hidden-print">
            <?
            $attributes['PageType']['field']->render($view);
            unset($attributes['PageType']);
            ?>
        </div>
    <?php } ?>


	<?php if ( $persisted && $baseline == '' && is_a($form->getObjectIt(), 'StatableIterator') && $attributes['State']['value'] != '' ) { ?>

    <div class="title-cell hidden-print" style="padding-top: 2px;">
    <?php
        $workflowActions = array();
        if ( is_array($actions['workflow']) ) {
            $workflowActions = $actions['workflow']['items'];
            unset($actions['workflow']['items']);
        }
        else {
            $workflowActions = array_filter($actions, function($action) {
                return strpos($action['uid'], 'workflow') !== false;
            });
            $actions = array_filter($actions, function($action) {
                return strpos($action['uid'], 'workflow') === false;
            });
        }
        echo $view->render('pm/StateColumn.php', array (
            'stateIt' => $form->getObjectIt()->getStateIt(),
            'actions' => $workflowActions
        ));
    ?>
    </div>

<?php } ?>

	<?php if ( $persisted && count($actions['create']['items']) > 0 ) { ?>
		<div class="title-cell hidden-print">
			<div class="btn-group last">
				<a tabindex="-1" class="btn btn-xs btn-success dropdown-toggle actions-button" data-toggle="dropdown" href="">
					<i class="icon-plus icon-white"></i>
				</a>
				<?php
				echo $view->render('core/PopupMenu.php', array (
					'items' => $actions['create']['items']
				));
				unset($actions['create']);
				?>
			</div>
		</div>
	<?php } // persisted ?>



<?php if ( $persisted && count($actions) ) { ?>

<div class="title-cell hidden-print">
    <div class="btn-group operation last">
      <a tabindex="-1" class="btn btn-xs btn-secondary dropdown-toggle actions-button" data-toggle="dropdown" href="">
		<i class="icon-pencil icon-white"></i>
		<span class="caret"></span>
      </a>
      <?php
          echo $view->render('core/PopupMenu.php', array (
              'items' => $actions
          ));
      ?>
    </div>
</div>
<?php } // persisted ?>

</div>

<input type="hidden" name="ParentPage" value="<?=$attributes['ParentPage']['value']?>">
<input type="hidden" name="treeTitle" value="<?=htmlentities($form->getObjectIt()->getTreeDisplayName($treeOptions))?>">

<div class="page-attachments">
<? if ( is_object($attachments) ) { echo $attachments->render($this); } ?>
    <div class="clearfix"></div>
</div>

<? echo $attributes_html; ?>

<? if ( is_object($attributes['Content']['field']) ) { $attributes['Content']['field']->draw(); } ?>

<? if ( $persisted ) echo $traces_html; ?>

<?php if ( $persisted ) { ?>
<div class="document-page-bottom hidden-print">
	<div class="hidden-print" style="width:100%;height:23px;">
		<div class="bottom-link">
			<span class="<?=($scrollable ? 'document-item-bottom-hidden' : '')?>">
				<? foreach( $structureActions as $key => $action ) { ?>
                    <? if ( $key == 'files' ) { ?>
                    <span class="document-structure-action">
						<i class="<?=$action['icon']?>"></i>
                        <a class="file-browse" post-action="refresh" objectclass="<?=get_class($form->getObjectIt()->object)?>" objectid="<?=$form->getObjectIt()->getId()?>" attachmentClass="WikiPageFile" tabindex="-1">
                            <?=$action['name']?>
                        </a>
                    </span>
                    <? } else { ?>
					<span class="document-structure-action">
						<i class="<?=$action['icon']?>"></i>
						<a class="" id="<?=$action['uid']?>" onclick="<?=$action['url']?>"><?=$action['name']?></a>
					</span>
                    <? } ?>
				<? } ?>
			</span>
		</div>
	</div>
</div>
<?php
}

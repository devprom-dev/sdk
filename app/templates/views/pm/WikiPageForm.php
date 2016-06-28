<?php 

$buttons = array();

if ( count($sections) > 0 )
{
	$comments_section = array_shift($sections);
}

?>

<div style="display:table;width:100%;">

<?php if ( $show_section_number && $attributes['SectionNumber']['value'] != '' ) { ?>

<h4 class="title-cell bs" style="width:1%;">
	<div class="sec-num">
    	<?=$attributes['SectionNumber']['value']?>
	</div>
</h4>

<?php } ?>

<h4 class="title-cell bs">
    <? $attributes['Caption']['field']->draw(); ?>
</h4>

<div class="title-cell" style="width:2%;"></div>

<?php if ( $revision['id'] != '' ) { ?>

<div class="title-cell" style="width:1%;">
	<span class="label label-inverse">
		<?=translate('Изменение').': '.$revision['id']?>
	</span>
</div>
<div class="title-cell" style="width:1%;"></div>

<?php } ?>

<?php if ( $persisted && $uid_icon != '' ) { ?>

<div class="title-cell" style="width:1%;"></div>
<div class="title-cell" style="width:1%;white-space:nowrap;">
	<? echo $view->render('core/Clipboard.php', array ('url' => $uid_url, 'uid' => $uid)); ?>
</div>

<?php } ?>

<?php if ( $persisted && $baseline == '' && is_a($form->getObjectIt(), 'StatableIterator') && $attributes['State']['value'] != '' ) { ?> 

<div class="title-cell" style="width:1%;"></div>
<div class="title-cell hidden-print" style="width:1%;">
<?php
	echo $view->render('pm/StateColumn.php', array (
				'color' => $form->getObjectIt()->get('StateColor'),
				'name' => $form->getObjectIt()->get('StateName'),
				'terminal' => $form->getObjectIt()->get('StateTerminal') == 'Y'
		)); 
?>
</div>

<?php } ?>

<? if ( count($compare_actions) > 0 ) { ?>

	<div class="title-cell" style="width:1%;"></div>
	<div class="title-cell hidden-print" style="width:1%;">
    <div class="btn-group operation last">
      <a tabindex="-1" class="btn btn-mini dropdown-toggle actions-button" data-toggle="dropdown" href="#">
    	<i class="icon-broken"></i>
    	<?=text(1725)?>
    	<span class="caret"></span>
      </a>
      <?php
    	echo $view->render('core/PopupMenu.php', array (
    		'items' => $compare_actions
    	));
      ?>
    </div>
</div>

<? } ?>

<?php if ( $persisted ) { ?>

<div class="title-cell" style="width:1%;"></div>

<div class="title-cell hidden-print" style="width:1%;">
    <div class="btn-group operation last">
      <a tabindex="-1" class="btn btn-mini dropdown-toggle actions-button" data-toggle="dropdown" href="#">
    	<i class="icon-asterisk icon-gray"></i>
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

<div class="page-attachments">
<? if ( is_object($attachments) ) { echo $attachments->render($this); } ?>
</div>

<? $attributes['Content']['field']->draw(); ?>
<? if ( $persisted ) echo $traces_html; ?>

<?php if ( $persisted && is_object($comments_section) ) { ?>
<div class="document-page-bottom hidden-print">
	<div style="display:table;width:100%;height:23px;">
		<div style="display:table-cell;">
			<span class="<?=($comments_count < 1 ? 'document-item-bottom-hidden': '')?>">
				<i class="icon-comment"></i>
				<a class="document-page-comments-link dashed" style="margin-top:3px;">
					<?=translate('комментарии').($comments_count > 0 ? ' ('.$comments_count.')' : '')?>
				</a>
			</span>
		</div>
		<div class="bottom-link" style="display:table-cell;text-align:right;vertical-align:top;width:70%;">
			<span class="document-item-bottom-hidden">
				<? foreach( $structureActions as $action ) { ?>
					<span class="document-structure-action">
						<i class="<?=$action['icon']?>"></i>
						<a class="dashed" onclick="<?=$action['url']?>"><?=$action['name']?></a>
					</span>
				<? } ?>
			</span>
		</div>
	</div>
	<div class="comments-section" style="display:none;">
		<?php $comments_section->render($this, array('new_link_class' => 'document-item-bottom-hidden')); ?>
	</div>
</div>
<?php } ?>

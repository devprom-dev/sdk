<?php 

$buttons = array();

if ( count($sections) > 0 )
{
	$comments_section = array_shift($sections);
}

?>

<div style="display:table;width:100%;">

<?php if ( $show_section_number ) { ?>

<h4 class="title-cell bs" style="width:1%;">
    <?=$attributes['SectionNumber']['value'].'&nbsp;&nbsp;'?>
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

<div class="title-cell" style="width:1%;white-space:nowrap;">
	<? echo $view->render('core/Clipboard.php', array ('url' => $uid_url, 'uid' => $uid)); ?>
</div>
<div class="title-cell" style="width:1%;"></div>

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


<div class="title-cell" style="width:2%;"></div>

<?php 

$props_url = $form->getObjectIt()->getViewUrl().'&properties=true&baseline='.$baseline;

$props_object = $form->getObjectIt()->object->getEntityRefName();

?>

<? if ( count($compare_actions) > 0 ) { ?>

<div class="title-cell hidden-print" style="width:1%;">
    <div class="btn-group operation last">
      <a tabindex="-1" class="btn btn-small dropdown-toggle actions-button" data-toggle="dropdown" href="#">
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

<?php if ( $has_properties ) { ?>

<div class="title-cell" style="width:1%;"></div>

<div class="title-cell filter-btn hidden-print" style="width:1%;">
   	<a class="btn dropdown-toggle btn-small dropdown-properties" style="white-space: nowrap;" onclick="javascript: workflowProperties('<?=$props_url?>', <?=$form->getObjectIt()->getId()?>, '<?=$props_object?>', '<?=text(1500)?>', 'donothing');" title="<?=text(1500)?>">
   		<i class="icon-align-justify"></i>
   	</a>
</div>

<?php } ?>

<div class="title-cell" style="width:1%;"></div>

<div class="title-cell hidden-print" style="width:1%;">
    <div class="btn-group operation last">
      <a tabindex="-1" class="btn btn-small dropdown-toggle actions-button" data-toggle="dropdown" href="#">
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

<? $attributes['Content']['field']->draw(); ?>

<?php if ( is_object($comments_section) ) { ?>
<div class="<?=($comments_count < 1 ? 'document-item-bottom' : '')?> hidden-print" style="display:table;width:100%;height:23px;">
	<div style="display:table-cell;">
		<?php if ( $comments_count > 0 ) { ?> 
		
		<a class="btn dropdown-toggle btn-mini btn-success dropdown-comments" href="#" title="<?=text(1501)?>" style="margin-top:3px;">
			<i class="icon-comment icon-white"></i>
			<?=$comments_count?>
		</a>
		
		<div class="comments-section" style="display:none;">
			<?php $comments_section->render($this, array('new_link_class' => 'document-item-bottom-hidden')); ?>
		</div>
		
		<?php } else if ( $baseline == '' ) { ?>
		
		<div class="comments-section">
			<?php $comments_section->render($this, array('icon_size' => 'small', 'new_link_class' => 'document-item-bottom-hidden')); ?>
		</div>
	
		<?php } ?>
	</div>

	<div class="bottom-link" style="display:table-cell;text-align:right;vertical-align:top;width:80px;">
		<span class="document-item-bottom-hidden">
			&uarr; <a class="dashed" onclick="javascript: $('body, html').animate({ scrollTop: 0 }, 50)"><?=translate('наверх')?></a>
		</span>
	</div>
</div>	
<?php } ?>

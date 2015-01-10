<div class="table-header">

<?php if ( is_array($changed_ids) ) foreach ( $changed_ids as $id ) { ?>

<div class="object-changed" object-id="<?=$id?>"></div>

<?php } ?>

<div class="filter hidden-print">

<?php if ( $title != '' ) { ?>

<ul class="breadcrumb">
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

<?php $filter_actions = $table->getFilterActions(); ?>

<?php if ( count($filter_actions) > 0 || count($filter_items) > 0 ) { ?>

<?php if ( !$tableonly && count($filter_actions) > 0 ) { ?>

<div class="btn-group pull-left" style="margin-right:5px;display:table;">
  <a id="filter-settings" class="btn dropdown-toggle btn-small" data-toggle="dropdown" href="#">
	<i class="icon-cog icon-gray"></i>
	<?php if ( $filter_actions[0]['name'] != '' ) { ?>
	<span class="caret"></span>
	<?php } ?>
  </a>
  <?php
  
	echo $view->render('core/PopupMenu.php', array (
		'items' => $filter_actions
	));
  ?>
</div>

<?php 

}

if ( !$tableonly )
{
foreach( $filter_items as $filter )
{
    if ( $filter['html'] != '' )
    {
        echo '<div class="btn-group pull-left">'.$filter['html'].'</div>';
    }
    else 
    {
    ?>
    <div class="btn-group pull-left">
    	<a class="btn btn-small dropdown-toggle <?=(in_array($filter['value'],array('','all'),true) ? '' : 'btn-info')?>" uid="<?=$filter['name']?>" href="#" data-toggle="dropdown">
    		<?=$filter['title']?>
    		<span class="caret"></span>
    	</a>
    	<? echo $view->render('core/PopupMenu.php', array ('items' => $filter['actions'])); ?>
    </div>
    <?php   
    }
}
}

?>

<?php } // if ( count($filter_actions) > 0 || count($filter_items) > 0 ) ?>

</div> <!-- end filter -->

<div class="hidden-print filter-actions">

<?php if ( !$tableonly && is_object($list) && !is_a($list, 'PageChart') ) { ?>

<?php foreach( $additional_actions as $action ) { ?>
	<?php if ( count(array_filter($action['items'], function($item){return $item['name'] != '';})) < 4 ) { ?>
		<?php foreach( $action['items'] as $key => $item ) { ?>
			<?php if ( $item['name'] == '' ) continue; ?>
			<div class="btn-group pull-left">
				<a id="<?=($item['uid'] != '' ? $item['uid'] : $key)?>" class="btn append-btn btn-small <?=($item['class'] == '' ? 'btn-success' : $item['class'])?>" href="<?=$item['url']?>">
			   		<i class="icon-plus icon-white"></i> <?=$item['name']?>
			   	</a>
			</div>
		<?php } ?>
	<?php } else { ?>
		<div class="btn-group pull-left">
			<a class="btn dropdown-toggle btn-small <?=($action['class'] == '' ? 'btn-success' : $action['class'])?>" href="#" data-toggle="dropdown">
		   		<?=$action['name']?>
		   		<span class="caret"></span>
		   	</a>
		   	<? echo $view->render('core/PopupMenu.php', array ('items' => $action['items'])); ?>
		</div>
   	<?php } ?>
<?php } ?>

<?php

    echo $view->render('core/PageSectionButtons.php', array (
        'sections' => $sections,
	    'object_class' => $object_class,
	    'object_id' => $object_id,
        'iterator' => $list->getIteratorRef()
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

<?php } // !PageChart ?>

</div> <!-- end actions -->

</div> <!-- table-header -->

<div class="clearfix"></div>

<?php if ( $filter_actions[0]['name'] != '' && $filter_modified ) { ?>

<?php 

$personal_script = "javascript: $('li[uid=personal-persist]>a').addClass('checked'); window.location = $('li[uid=personal-persist]>a[href]').length > 0 ? $('li[uid=personal-persist]>a').attr('href') : $('li[uid=personal-persist]>a').attr('onkeydown')";
$project_script = 'javascript: window.location = $(\'li[uid=project-persist]>a\').attr(\'onkeydown\')';

$alert_text = str_replace('%2', $project_script, str_replace('%1', $personal_script, text(1318))); 

?>

<div class="hidden-print alert alert-filter alert-warning"><?=$alert_text?></div>

<?php } ?>

<div id="tablePlaceholder">
    <?php 
    	is_object($list) 
    		? $list->render( $view, array(
    					'object_id' => $object_id,
    					'object_class' => $object_class
    				)) 
    		: $table->draw( $view ); 
    ?>
</div>

<?php $is_need_navigator = $table->IsNeedNavigator() && is_object($list) && $list->moreThanOnePage(); ?>

<?php if ( is_object($list) && $is_need_navigator ) $list->drawNavigator(false); else $table->drawFooter(); ?>

<?php if ( !$tableonly ) { ?> 

<?php $table->drawScripts(); ?>

<?php } ?>

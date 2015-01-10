<?php 

$view->extend('core/PageBody.php'); 

$view['slots']->output('_content');

?>
 
<div class="table-header">
<div class="filter">

<?php $filter_actions = $table->getFilterActions(); ?>

<?php if ( count($filter_actions) > 0 || count($filter_items) > 0 ) { ?>

<?php if ( !$tableonly && count($filter_actions) > 0 ) { ?>

<div class="btn-group pull-left" style="margin-right:5px">
  <a id="filter-settings" class="btn dropdown-toggle btn-small" data-toggle="dropdown" href="#">
	<i class="icon-cog icon-gray"></i>
	<span class="caret"></span>
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
    <div class="btn-group pull-left" style="margin-right:5px">
    	<a class="btn btn-small dropdown-toggle <?=(in_array($filter['value'],array('','all'),true) ? '' : 'btn-info')?>" href="#" data-toggle="dropdown">
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


<div class="btn-group">
   	<a class="btn btn-warning btn-small" href="<?=$news_url?>">RSS</a>
</div>

</div> <!-- end filter -->

<?php if ( count($actions) > 0 ) { ?>

<div class="filter-actions last">

	<?php foreach( $additional_actions as $action ) { ?>
		<?php foreach( $action['items'] as $item ) { ?>
			<div class="btn-group pull-left">
				<a class="btn btn-small btn-success" href="<?=$item['url']?>">
			   		<i class="icon-plus icon-white"></i> <?=$item['name']?>
			   	</a>
			</div>
	    <?php } ?>
	<?php } ?>
			
	<div class="btn-group pull-left last">
		<a class="btn dropdown-toggle btn-small btn-inverse" href="#" data-toggle="dropdown">
    		<?=translate('Действия')?>
    		<span class="caret"></span>
    	</a>
    	<? echo $view->render('core/PopupMenu.php', array ('items' => $actions)); ?>
    </div>
</div>

<?php } ?>

</div> <!-- table-header -->

<?php 

$table->draw( $view, $post_it ); 

?>

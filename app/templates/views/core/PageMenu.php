<div class="btn-toolbar" style="padding-right:12px;">



<?php if ( count($checkpoint_alerts) > 0 ) { ?>
<?php 
foreach( $checkpoint_alerts as $key => $alert )
{
	$checkpoint_alerts[$key] = ($key + 1).'.'.str_replace(' ', '&nbsp;', $checkpoint_alerts[$key]).':&nbsp;<font color="red">'.translate('нет').'</font>';
}
?>
<div class="btn-group last">
	<a class="btn btn-navbar btn-link" href="<?=$checkpoint_url?>">
		<?=text(1128)?>
	</a>
</div>	<!-- end btn-group -->	
<div class="btn-group last">
	<a class="btn btn-navbar btn-danger with-tooltip" 
	   placement="bottom"
	   loaded="true" 
	   info="" 
	   href="<?=$checkpoint_url?>"
	   data-content="<?=htmlentities(join('<br/>',$checkpoint_alerts), ENT_QUOTES | ENT_HTML401, APP_ENCODING)?>">
			<b><?=count($checkpoint_alerts)?></b>
	</a>
</div>	<!-- end btn-group -->	

<div class="btn-group last">
</div>	<!-- end btn-group -->	

<div class="btn-group last">
</div>	<!-- end btn-group -->	

<div class="btn-group last">
</div>	<!-- end btn-group -->	
<?php } ?>




<?php foreach( $menus as $key => $menu ) { ?>

<?php if ( $menu['button_class'] != '' ) { ?>

<div class="btn-group last">
	<?php if ( $menu['title'] != '' ) { ?>
		<?php if ( $menu['url'] != '' ) { ?>
		<a id="<?=$menu['id']?>" class="btn <?=$menu['button_class']?>" href="<?=$menu['url']?>" title="<?=$menu['description']?>">
		<?php } else { ?>
		<a id="<?=$menu['id']?>" class="btn dropdown-toggle <?=$menu['button_class']?>" data-toggle="dropdown" href="#" title="<?=$menu['description']?>">
		<?php } ?>
			<?php if ( $menu['icon'] != '' ) { ?>
				<i class="<?=$menu['icon']?>"></i>
			<?php } ?>
			<?=$menu['title']?>
			<?php if ( count($menu['items']) > 0 ) { ?>
				<span class="caret"></span>
			<?php } ?>
		</a>
		<?php if ( count($menu['items']) > 0 ) { ?>
			<?php
				echo $view->render('core/PopupMenu.php', array (
					'class' => $menu['class'],
					'title' => $menu['title'],
					'items' => $menu['items']
				));
			?>
		<?php } ?>
	<?php } ?>
</div>	<!-- end btn-group -->	

<?php } else { ?>

<div class="btn-group last">
  <a id="navbar-user-menu" class="btn btn-link btn-navbar dropdown-toggle" data-toggle="dropdown" href="#">
	<?=$menu['title']?>
	<span class="caret"></span>
  </a>
	<?php
	
	echo $view->render('core/PopupMenu.php', array (
		'class' => $menu['class'],
		'title' => $menu['title'],
		'items' => $menu['items']
	));
	
	?>
</div>	<!-- end btn-group -->	

<?php } ?>

<?php } ?>

</div> <!-- end btn-toolbar -->


<div class="btn-toolbar" style="padding-right:12px;">

<?php foreach( $menus as $key => $menu ) { ?>

<?php if ( $menu['button_class'] != '' ) { ?>

<div class="btn-group last">
	<?php if ( $menu['title'] != '' ) { ?>
		<?php if ( $menu['url'] != '' ) { ?>
		<a id="navbar-quick-create" class="btn <?=$menu['button_class']?>" href="<?=$menu['url']?>" title="<?=$menu['description']?>">
		<?php } else { ?>
		<a id="navbar-quick-create" class="btn dropdown-toggle <?=$menu['button_class']?>" data-toggle="dropdown" href="#">
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


<div class="btn-toolbar tools-toolbar">
	<?php if ( count($checkpoint_alerts) > 0 ) { ?>
		<?php
		foreach( $checkpoint_alerts as $key => $alert )
		{
			$checkpoint_alerts[$key] = ($key + 1).'. '.$checkpoint_alerts[$key];
		}
		?>
		<div class="btn-group last">
			<a class="btn btn-navbar btn-danger with-tooltip"
			   placement="bottom"
			   loaded="true"
			   info=""
			   href="<?=$checkpoint_url?>"
			   data-content="<?=htmlentities(text(1128).'<br/><br/>'.join('<br/>',$checkpoint_alerts), ENT_QUOTES | ENT_HTML401, APP_ENCODING)?>">
				<b><?=count($checkpoint_alerts)?></b>
			</a>
		</div>	<!-- end btn-group -->
		<div class="btn-group last">
		</div>	<!-- end btn-group -->
	<?php } ?>

	<? if ( count($areas) > 1 || array_shift(array_keys($areas)) == 'favs' ) { ?>
	<div class="btn-group"></div>
	<div class="btn-group last">
		<form class="form-search" action="<?=$search_url?>">
			<div class="input-append">
				<? $content = htmlentities($view->render('core/PageMenuShort.php',array('areas' => $areas))); ?>
				<input id="quick-search" name="search-keywords" type="text" class="search-query" placeholder="<?=text(2195)?>" object="Widget" searchattrs="Caption,ReferenceName" additional="" data-content="<?=$content?>">
				<button type="submit" class="btn medium-blue">
					<i class="icon-search"></i>
				</button>
			</div>
		</form>
	</div>
	<? } ?>


<?php foreach( $menus as $key => $menu ) { ?>

<?php if ( $menu['button_class'] != '' ) { ?>

<div class="btn-group last">
	<?php if ( $menu['button_class'] != 'empty' ) { ?>
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
  <a id="navbar-user-menu" class="dropdown-toggle" data-toggle="dropdown" href="#">
	<?=$menu['title']?>
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


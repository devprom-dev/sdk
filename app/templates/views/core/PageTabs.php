<div class="navbar">
	<div class="navbar-inner">
		<ul class="nav" style="width:100%;">
		
		<?php foreach( $pages as $key => $page ) { ?>
			<?php if ( $page['type'] == 'hidden' ) continue; ?>
			<?php if ( $key == 'stg' ) { $style = ';'; } else { $style = ''; } ?>
			
			<li id="tab_<?=$page['uid']?>" area="<?=$page['uid']?>" class="dropdown <?=($page['uid'] == $active_area_uid ? 'active open' : '')?>" style="<?=$style?>">
				<a data-toggle="dropdown" class="dropdown-toggle transparent-btn" href="<?=$page['url']?>">
				
				    <?php if ( $page['icon'] != '' ) { ?> <i class="<?=$page['icon']?>"></i> <?php } ?>
				
					<?=trim($page['name'], '.')?>
					<?php if ( is_array($page['items']) ) { ?><b class="caret"></b> <?php } ?>
				</a>
				<?php
				
				if ( !is_array($page['items']) ) continue;
				 
				echo $view->render('core/PopupMenu.php', array (
					'class' => "menuitem_popup ".($page['active'] ? 'active' : ''),
					'title' => trim($page['name'], '.'),
					'items' => $page['items'],
					'url' => $page['url']
				));

				?>
			</li>
			
		<?php } ?>        		
		
		</ul>
	</div>
</div>

<script type="text/javascript">
    $('.nav > li.dropdown > a').click( function() 
    {
    	$('.nav > li.dropdown').removeClass('active');

    	$(this).parent('li').addClass('active');

    	$('.vertical-menu').hide();

    	var menu = $('#menu_'+$(this).parent('li').attr('area'));
    	
    	menu.show();

    	adjustContainerHeight(menu);    	
    });

    $(document).ready( function() {
    	adjustContainerHeight($('#menu_<?=$active_area_uid?>'));
    });
</script>

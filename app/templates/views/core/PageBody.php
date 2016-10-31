<?php $view->extend('core/Page.php'); ?>

<div class="row-fluid hidden-print">
	<div class="pull-left">
		<?php echo $view->render($caption_template, $navigation_parms); ?>
	</div>
	<div class="pull-right">
		<?php 
			echo $view->render('core/PageMenu.php', array_merge($navigation_parms, array(
				'checkpoint_alerts' => $checkpoint_alerts,
				'checkpoint_url' => $checkpoint_url,
				'areas' => $bodyExpanded ? $navigation_parms['areas'] : array(),
				'search_url' => $search_url
			)));
		?>
	</div> 
</div> <!-- end row -->

<?php if ( !$bodyExpanded && $has_horizontal_menu ) { // functional areas, horizontal menu ?>
<header class="navbar hidden-print">
    <div class="row-fluid">
    	<div class="span12">
    		<?php 
    		
   			echo $view->render('core/PageTabs.php', array(
                'pages' => $navigation_parms['areas'],
                'active_area_uid' => $active_area_uid
            ));
    		
    		?>
    	</div>
    </div> <!-- end row-fluid -->
</header>
<?php } ?>

<div id="main" role="main" class="container-fluid container-fluid-internal">
	<div class="contained">
		<?php
		if ( !$bodyExpanded && count($navigation_parms['areas']) > 0 )
		{
			?>
			<!-- aside -->
			<aside class="hidden-print span2" style="margin:0;">
				<!-- aside item: Menu -->
				<div id="sidebar">
				<?php

				foreach( $navigation_parms['areas'] as $area ) {
					echo $view->render('core/VerticalMenu.php', array(
						'items' => $area['menus'],
						'area_id' => count($navigation_parms['areas']) > 1 ? $area['uid'] : $active_area_uid,
						'active_area_uid' => $active_area_uid,
						'active_url' => $active_url,
						'application_url' => $application_url,
						'search_url' => $search_url
					));
				}

				?>
				</div>
			</aside> <!-- aside -->
			<?php
		}
		elseif ( $bodyExpanded && count($navigation_parms['areas']) > 0 )
		{
			?>
			<aside class="hidden-print" style="margin:0;width:60px;">
				<!-- aside item: Menu -->
				<div id="sidebar">
				<?php

					$area = array_shift($navigation_parms['areas']);
					echo $view->render('core/VerticalShortMenu.php', array(
						'items' => $area['menus'],
						'area_id' => $area['uid'],
						'active_url' => $active_url,
						'application_url' => $application_url
					));

				?>
				</div>
			</aside> <!-- aside -->
			<?php
		}
		else { ?>
			<?php $style = "margin-left:20px;"; ?>
		<?php } ?>
		
        <div id="page-content" class="container-fluid" style="padding:0">
            <section class="content content-internal <?=($bodyExpanded ? 'content-expanded' : '')?> <?=$section_class?>" style="<?=$style?>" module="<?=$module?>" report="<?=$report?>">
                <div class="row-fluid">
               		<?php $view['slots']->output('_content') ?>
               	</div> <!-- end row-fluid -->
            </section>
        </div>
		
	</div>
</div>

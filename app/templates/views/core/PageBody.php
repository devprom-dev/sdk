<?php $view->extend('core/Page.php'); ?>

<? if ( $b_checkpoint_alert ) { ?>

<div class="alert alert-error" style="text-align:center;"><?=text(1128)?></div>

<?php } ?>
  
<div class="row-fluid">
	<div class="pull-left">
		<?php 
		
			echo $view->render($caption_template, array( 
					'project_navigation_parms' => $project_navigation_parms, 
					'data' => $caption_data, 
					'company_name' => $company_name, 
					'tab_uid' => $tab_uid,
					'current_version' => $current_version,
					'language_code' => $language_code,
					'javascript_paths' => $javascript_paths
				)); 
		?>
	</div>
	<div class="pull-right">
		<?php echo $view->render($menu_template, array( 'menus' => $menus )); ?>
	</div> 
</div> <!-- end row -->

<?php if ( $has_horizontal_menu ) { // functional areas, horizontal menu ?>
<header class="navbar">
    <div class="row-fluid">
    	<div class="span12">
    		<?php 
    		
   			echo $view->render('core/PageTabs.php', array_merge($tabs_parms, array(
                'pages' => $areas,
                'active_area_uid' => $active_area_uid
            )));
    		
    		?>
    	</div>
    </div> <!-- end row-fluid -->
</header>
<?php } ?>

<div id="main" role="main" class="container-fluid container-fluid-internal">
	<div class="contained">
		<?php if ( count($areas) > 0 ) { ?>
		<!-- aside -->	
		<aside class="span2" style="margin:0;">	
			<!-- aside item: Menu -->
			<div id="sidebar">
			<?php
			
		    foreach( $areas as $area ) 
            {
			        echo $view->render('core/VerticalMenu.php', array( 
                        'items' => $area['menus'], 
                        'area_id' => count($areas) > 1 ? $area['uid'] : $active_area_uid, 
                        'active_area_uid' => $active_area_uid,
                        'application_url' => $application_url
                    ));
            }
			
			?>
			</div>
		</aside> <!-- aside -->
		<?php } else { ?>
			<?php $style = "margin-left:20px;"; ?>
		<?php } ?>
		
        <div id="page-content" class="container-fluid" style="padding:0">
            <section class="content content-internal" style="<?=$style?>">
                <div class="row-fluid">
                
               		<?php $view['slots']->output('_content') ?>

               		<?php 
					if ( $hint != '' )
					{
						echo $view->render('core/Hint.php', array('title' => $hint, 'name' => $page_uid));
					}
               		?>
               		
               	</div> <!-- end row-fluid -->
            </section>
        </div>
		
	</div>
</div>

<?php
$project_navigation_parms['projectSortData'] = $projectSortData;
?>
<div class="btn-toolbar title-toolbar">
	<div class="btn-group navbar-company">
    	<a id="navbar-project" class="btn btn-link btn-navbar dropdown-toggle" data-toggle="dropdown" href="#">
    	    <i class="icon-th-list icon-white"></i>&nbsp;
    		<?=$company_name?>
    		<span class="caret"></span>
    	</a>
    	<?php 

		echo $view->render('core/PageTitleCompany.php', $project_navigation_parms);

        ?>

        <?php
        if ( is_array($quickMenu) && count($quickMenu['items']) > 0 ) {
            ?>
            <div class="btn-group quick-btn">
                <a id="<?=$quickMenu['id']?>" class="btn dropdown-toggle btn-sm <?=$quickMenu['button_class']?>" data-toggle="dropdown" href="#" title="<?=$quickMenu['description']?>">
                    <i class="<?=$quickMenu['icon']?>"></i>
                </a>
                <?php
                echo $view->render('core/PopupMenu.php', array (
                    'class' => $quickMenu['class'],
                    'title' => $quickMenu['title'],
                    'items' => $quickMenu['items']
                ));
                ?>
            </div>	<!-- end btn-group -->
            <?php
        }
        ?>

	</div>
</div> <!-- end btn-toolbar -->


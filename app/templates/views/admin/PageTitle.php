<div class="btn-toolbar title-toolbar">

    <?php if ( $company_name != '' ) { ?>
    <div class="btn-group">
    	<a id="navbar-company-name" class="btn btn-link btn-navbar dropdown-toggle" data-toggle="dropdown" href="#">
    	    <i class="icon-th-list icon-white"></i>&nbsp;
    		<?=$company_name?>
    		<span class="caret"></span>
    	</a>

    	<?php echo $view->render('core/PageTitleCompany.php', $project_navigation_parms); ?>
	</div>
	<?php } ?>
	
	<div class="btn-group">
    	<a id="navbar-administration" class="btn btn-link btn-navbar dropdown-toggle" data-toggle="dropdown" href="#">
		    <?=translate('Администрирование')?> 
        </a>
	</div>
</div> <!-- end btn-toolbar -->
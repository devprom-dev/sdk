<?php

$session = getSession();

$project_it = $session->getProjectIt();

?>

<div class="btn-toolbar title-toolbar">
	<div class="btn-group">
	  
	  <a id="navbar-company-name" class="btn btn-link btn-navbar dropdown-toggle" data-toggle="dropdown" href="#">
	  <i class="icon-th-list icon-white"></i>&nbsp;
		<?=$company_name?> 
		<span class="caret"></span>
	  </a>
	  
	  <?php if ( is_array($project_navigation_parms) ) { ?> 

		<?php echo $view->render('core/PageTitleCompany.php', $project_navigation_parms); ?>
		
	  <? } ?>
	</div>

	<div class="btn-group">
		<a class="btn btn-link btn-navbar">
    	</a>
	</div>    	
	
	<?php $subprojects_count = count($project_navigation_parms['projects'][$project_navigation_parms['current_portfolio']]); ?>
	<?php if( is_array($project_navigation_parms) ) { ?>
	
	<?php if ( $project_navigation_parms['current_project'] == $project_navigation_parms['current_portfolio'] && $subprojects_count > 0 ) { ?>

	<div class="btn-group">
	  <a id="navbar-portfolio" class="btn btn-link btn-navbar dropdown-toggle" data-toggle="dropdown" href="#">
		<?=$project_navigation_parms['current_portfolio_title']?> 
		<span class="caret"></span>
	  </a>
	  
	  <?php echo $view->render('core/PageTitlePortfolios.php', $project_navigation_parms); ?>
	</div>		
	
	<?php } else { ?>

	<div class="btn-group">
	  <a class="btn btn-link btn-navbar" href="/pm/<?=$project_navigation_parms['current_portfolio']?>">
		<?=$project_navigation_parms['current_portfolio_title']?> 
	  </a>
	</div>		

	<div class="btn-group">
		<a class="btn btn-link btn-navbar">
    	</a>
	</div>    	
	
	<div class="btn-group">
	
    <?php if( $subprojects_count > 1 ) { ?>
  		<a id="navbar-project" class="btn btn-link btn-navbar dropdown-toggle" data-toggle="dropdown" href="#">
    		<?=$project_navigation_parms['current_project_title']?> 
    		<span class="caret"></span>
  		</a>
  		
  	  <?php 
  	  if( count($project_navigation_parms['projects'][$project_navigation_parms['current_portfolio']]) > 1 )
	  {
	  	echo $view->render('core/PageTitleProjects.php', $project_navigation_parms);
	  }
	  ?>
  		
    <?php } elseif ( $subprojects_count > 0 ) { ?>
  		<a id="navbar-project" class="btn btn-link btn-navbar" href="/pm/<?=$project_navigation_parms['current_project']?>">
    		<?=$project_navigation_parms['current_project_title']?> 
    	</a>
    <?php } ?>

	</div>
	
	<?php } ?>
	
	<?php } ?>		

    <?php if ( $project_it->IsPortfolio() && in_array($project_it->get('LinkedProject'), array('','0')) ) { ?>
    
    <a id="navbar-create-project" class="btn btn-warning" href="/projects/new">
    	<i class="icon-plus icon-white"></i> <?=translate('Создать проект')?>
    </a>
            
    <?php } ?>

</div> <!-- end btn-toolbar -->


<script type="text/javascript">
	devpromOpts.project = '<?=$project_it->get('CodeName')?>';

	if ( $.browser.msie && document.documentMode < 8 )
	{
		translate( 1352, function( text ) 
		{
			$('<div class="alert alert-error" style="text-align:center;">'+text+'</div>').insertBefore('.container-fluid:eq(0)');
	    });
	}
</script>


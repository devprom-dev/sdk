<?php $view->extend('core/PageBody.php'); ?>
<?php $view['slots']->start('_header'); ?>

<link rel="stylesheet" href="/pm/bundles/Devprom/ProjectBundle/Resources/public/css/styles.css" />

<?php $view['slots']->stop(); ?>
    
		    <div class="span12">
			<div class="row-fluid" style="min-height: 45px;">
			    <div class="span10">
					<section id="functional-group-selector">
					</section><!-- end #functional-group-selector -->
				</div>
				<div class="span2">
					<a class="pull-right btn dropdown-toggle btn-inverse" href="<?=$close_url?>"><?=translate('Закрыть')?></a>
				</div>
			</div>
			<div class="row-fluid">
				<?php if ( $hint_top != '' ) { ?>
					<?php echo $view->render('core/HintLight.php', array('title' => $hint_top, 'name' => 'navigations-hint')); ?> 
				<? } ?>
			</div>
			<div class="row-fluid">
			    <section id="menu-configurator">
					<table class="table table-bordered">
						<tr>
							<th width="30%">
					    		<?=text(1808)?>
							</th>
							<th width="70%">
					    		<?=text(1809)?>
							</th>
						</tr>
						<tr>
							<td>
								<div class="menu-colum">
								    <div class="row-fluid">
										<section id="menu">
											<div class="container-fluid">
											  <div class="row-fluid">
											    <div class="span8">
											      <div class="menu-content"></div>
											      <div class="bottom" id="addMenuNode"></div>
											    </div>
											    <div class="span4">
											      <div class="menu-button pull-right" id="save-control"></div>
											      <div class="menu-button pull-right" id="restore-control"></div>
											      </div>
											  </div>
											</div>
										</section>
								    </div>
								    <div class="row-fluid">
								    </div>
								</div>
							</td>
							<td>
								<div class="pages-column">
								    <section id="pages">	
										<form class="filter">
										    <div class="input-append" style="width:100%;">
											<input id="appendedInput" class="span11" type="text" placeholder="<?=text(1810)?>">
											<span class="add-on"><i class="icon-search"></i></span>
										    </div>
										</form>
										<div class="pages-content"></div>
								    </section><!-- end #pages -->
								</div><!-- end .pages-column -->
							</td>
						</tr>				
					</table>			
			    </section><!-- end #menu-configurator -->
			</div>
		    </div><!-- end .span9 -->

	<!-- Backbone templates here -->
	<script id="node-template" type="text/template">
	    <span><%= title %></span>
	    <ul></ul>
	</script>

	<script id="page-list-node-template" type="text/template">
	    <div class="hdr"><%= title %></div>
		<% if ( id == 'favs' ) { %>
		<div class="hdr-ref"><a target="_blank" href="<?=$reports_edit_url?>"><?=translate('редактировать')?></a></div>
		<% } else if ( reportUrl != '' ) { %>
		<div class="hdr-ref"><a target="_blank" href="<%= reportUrl %>"><?=translate('просмотр')?></a></div>
		<% } %>
		<div class="desc"><%= desc %></div>
	    <ul></ul>
	</script>
	
	<script id="menu-group-node" type="text/template">
	    <div class="menu-group-node">
		<span><%= title %></span>
		<div class="controls">
		    <i class="controls"><i class="icon-edit"></i>&nbsp;<i class="icon-remove"></i></i>
		</div>
	    </div>
	    <ul></ul>
	</script>

	<script id="function-space-item" type="text/template"><a href="#<%= id %>" uid="<%= id %>"><%= title %></a></script>		    
	<!-- / tempates -->
	
	
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/libs/backbone.marionette/json2.js" type="text/javascript" charset="UTF-8"></script>
	<!--<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/libs/backbone.marionette/jquery.js" type="text/javascript" charset="UTF-8"></script>-->
	
	<!--<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/libs/jquery-ui-1.10.3.js" type="text/javascript" charset="UTF-8"></script>-->
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/libs/jquery-blindover.js" type="text/javascript" charset="UTF-8"></script>
	
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/libs/backbone.marionette/underscore.js" type="text/javascript" charset="UTF-8"></script>
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/libs/backbone.marionette/backbone.js" type="text/javascript" charset="UTF-8"></script>
	
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/libs/backbone.marionette/backbone.marionette.js" type="text/javascript" charset="UTF-8"></script>
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/libs/backbone.marionette/backbone.babysitter.js" type="text/javascript" charset="UTF-8"></script>
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/libs/backbone.treemodel.js" type="text/javascript" charset="UTF-8"></script>
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/libs/underi18n.js" type="text/javascript" charset="UTF-8"></script>
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/locals/<?=$language_code?>/resource.js" type="text/javascript" charset="UTF-8"></script>
	
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/app.js" type="text/javascript" charset="UTF-8"></script>
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/models/MenuGroupItem.js" charset="UTF-8"></script>
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/models/MenuItem.js" charset="UTF-8"></script>
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/collections/Menu.js" charset="UTF-8"></script>
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/models/Report.js" charset="UTF-8"></script>
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/collections/ReportCategory.js" charset="UTF-8"></script>
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/models/FunctionalArea.js" charset="UTF-8"></script>
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/collections/FunctionalAreaSet.js" charset="UTF-8"></script>
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/views/TreeView.js" type="text/javascript" charset="UTF-8"></script>
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/views/InlineAdder.js" type="text/javascript" charset="UTF-8"></script>
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/views/DragDrop.js" type="text/javascript" charset="UTF-8"></script>
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/views/GroupMenuNodeView.js" type="text/javascript" charset="UTF-8"></script>
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/views/FilterableTreeNodeView.js" type="text/javascript" charset="UTF-8"></script>
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/views/SaveButtonView.js" type="text/javascript" charset="UTF-8"></script>
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/views/RestoreButtonView.js" type="text/javascript" charset="UTF-8"></script>
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/modules/Pages.js" type="text/javascript" charset="UTF-8"></script>
	<script src="/pm/bundles/Devprom/ProjectBundle/Resources/public/js/modules/MenuConfigurator.js" type="text/javascript" charset="UTF-8"></script>

	<script type="text/javascript">
	$(function() {
	    App.module('MenuConfigurator').restUrl = '/pm/<?= getSession()->getProjectIt()->get('CodeName'); ?>/menu/rest';
	    App.module('MenuConfigurator').currentArea = '<?=htmlentities($_REQUEST['area'])?>';
	    
	    App.start({});
	    
	    //disabling filterHandler functionality
	    $('body, .content-internal').off('click.dropdown.data-api');    
	});
	</script>
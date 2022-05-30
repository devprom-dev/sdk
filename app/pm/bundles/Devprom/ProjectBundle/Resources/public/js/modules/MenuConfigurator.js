/* 
 * The MenuConfigurator module. Connects all components to provide menu configuration functionality
 * 
 * @author Vasiliy Pedak truvazia@gmail.com
 */
var App = App || {};

App.module("MenuConfigurator", function(MenuConfigurator, App, Backbone, Marionette, $, _){
    //Public 
    
    this.functionSpacesRegion = new Backbone.Marionette.Region({el: $('#functional-group-selector')});
    
    //The region for displaing menu structure
    this.menuRegion = new Backbone.Marionette.Region({el: $('div.menu-content')});
    this.saveControlRegion = new Backbone.Marionette.Region({el: '#save-control'});
    this.restoreControlRegion = new Backbone.Marionette.Region({el: '#restore-control'});
    this.currentArea = '';
    this.restUrl = '';

    this.getRestUrl = function() {
    	return MenuConfigurator.restUrl;
    },
    
    this.resetCurrentMenu = function() {
    	if ( typeof menuNodesCollection != 'undefined' ) {
			menuNodesCollection.set({});
		}
    };
    
    //Private
    var MenuView = TreeView.extend({
	    itemView: GroupMenuNodeView
	}),
	
	//Single functional area button selector view
	FunctionSpaceView = Marionette.ItemView.extend({
	    tagName: 'li',
	    template: '#function-space-item',
	    selectedClasses: 'active',
	    
	    events: {
		"click": "select" 
	    },
	    
	    select: function(e){
		$(this.tagName, this.$el.parent()).removeClass(this.selectedClasses);
		this.$el.addClass(this.selectedClasses);
		menuNodesCollection.reset(this.model.get('menuNodes'));
		this.trigger('selected');
	    }
	}),
	
	FunctionSpacesView = Marionette.CollectionView.extend({
	    tagName: 'ul',
	    attributes:{
		"class": 'nav nav-pills',
		"style": 'margin-bottom: 0px'
	    },
	    itemView: FunctionSpaceView,
	    
	    initialize: function(){
		this.on("itemview:selected", this.onSelected);
	    },
	    
	    onSelected: function(childview){
		console.log('Was selected');
		this.selected = childview;
	    }
	}),
	
	functionalAreas, functionalAreasView, menuNodesCollection, menuView, saveButton, restoreButton;
    
    this.addInitializer(function(){
	console.log('MenuConfigurator initializer...');
	/**
	 * Collection with FunctionalArea models
	 * @type FunctionalAreaSet
	 */
	functionalAreas = new FunctionalAreaSet([], {url: this.getFunctionalAreasUrl}),
	
	// Functional area view
	functionalAreasView = new FunctionSpacesView({collection: functionalAreas}),

	menuNodesCollection = new Menu(),
	
	menuView = new MenuView({
	    collection: menuNodesCollection
	}),
	
	saveButton = new SaveButtonView({
	    changesSource: menuNodesCollection,
	    changeEvents: 'add menuitemremoved menugroupchanged menuGroupItemEdited',
	    maskSelector: '#menu, #functional-group-selector, #pages div.pages-content'
	});
	
	restoreButton = new RestoreButtonView({
	    changesSource: menuNodesCollection,
	    changeEvents: 'add menuitemremoved menugroupchanged menuGroupItemEdited',
	    maskSelector: '#menu, #functional-group-selector, #pages div.pages-content'
	});

	MenuConfigurator.menuGroupAdder = new InlineAdder({
	    el: $('#addMenuNode'),
	    buttonText: t('Add menu group'),
	    collection: menuNodesCollection,
	    property: 'title'
	}); 
	
	MenuConfigurator.dragDrop = new DragDrop({
	    el: $('#menu-configurator'),
	    draggableSelector: 'div.pages-content li li div.hdr',
	    droppableSelector: 'div.menu-content li li',
	    destinationView: menuView,
	    sourceView: App.Pages.pagesView
	});

	//Redraw menu after changes
	MenuConfigurator.listenTo(menuNodesCollection, 'reset add menugroupchanged menuitemremoved', function(){
	    console.log('menugroupchanged in Menu collection');
	    this.menuRegion.show(menuView);
	    this.dragDrop.initDragDrop();
	});

	//save selected functional area after button clicked
	MenuConfigurator.listenTo(saveButton, 'savingstarted', function()
	{
	    console.log('saving started...');
	    var currentFunctionalArea = functionalAreasView.selected.model;
	    currentFunctionalArea.set('menuNodes', menuNodesCollection);
	    currentFunctionalArea.save({}, {
		success: function(model, response, options){
		    console.log('saving done');
		    menuNodesCollection.reset(model.get('menuNodes'));
		    saveButton.saved();
		},
		error: function(model, xhr, options){
		    console.log('Error during saving: ' + xhr);
		}
	    });
	});

	//restore (delete) selected functional area settings
	MenuConfigurator.listenTo(restoreButton, 'savingstarted', function()
	{
	    console.log('restoring started...');
	    var currentFunctionalArea = functionalAreasView.selected.model;
	    currentFunctionalArea.save({}, {
	    patch: true,
		success: function(model, response, options){
		    console.log('restoring done');
		    menuNodesCollection.reset(response.menuNodes);
		    restoreButton.saved();
		},
		error: function(model, xhr, options){
		    console.log('Error during restoring: ' + xhr);
		}
	    });
	});

});
    
App.on("start", function()
{
	//Load available pages
        App.Pages.pagesCollection.fetch({ 
	    reset: true,
	    success: function(){

		console.log('App.Pages.pagesCollection.fetch in success');
		App.Pages.pagesView.$el.appendTo('div.pages-content');
		MenuConfigurator.dragDrop.attachSourceHandlers();

		//Load all functional areas models
		functionalAreas.fetch({success: function(){
		    MenuConfigurator.functionSpacesRegion.show(functionalAreasView);
		    MenuConfigurator.saveControlRegion.show(saveButton);
		    MenuConfigurator.restoreControlRegion.show(restoreButton);
		    functionalAreasView.on('itemview:selected', function(){saveButton.reset();});

		    MenuConfigurator.menuGroupAdder.render();

		    functionalAreasView.children.each(function(view) {
		    	if( $(view.el).has('a[uid="'+App.module('MenuConfigurator').currentArea+'"]').length > 0 )
		    	{
		    		view.select();
		    	};
		    });

			if ( devpromOpts.uiExtensionsEnabled ) {
				setTimeout(function() {
					$('.pages-column, .menu-colum').each(function() {
						var scrollbar = new PerfectScrollbar($(this).get(0), {
							suppressScrollX: false
						});
					});
				}, 500);
			}

			}});
	    }
        });
    });

	$('#menu-reset').click(function() { App.module("MenuConfigurator").resetCurrentMenu(); });
});

/* 
 * Provides functionality for dragging items from one CompositeView to another
 * 
 * Note. This is depends from MenuConfigurator, so refactoring needed
 * 
 * @TODO: Make it customizable, refactor it
 * 
 * @author Vasiliy Pedak truvazia@gmail.com
 */
var DragDrop = Backbone.Marionette.View.extend({
    
    sourceView: undefined,
    
    destinationView: undefined,
    
    $sortable: undefined,
    
    $draggable: undefined,
    
    $droppable: undefined,
    
    // We need to wait for rendering of destination and source views
    isSourceReady: false,
    isDestinationReady: false,
    
    //Current dragging view
    dragView: undefined,
    //Represents the view type ( destination or source ) where dragging have started
    dragSource: false,
    
    initialize: function(){
	this.draggableSelector = this.options.draggableSelector;
	this.droppableSelector = this.options.droppableSelector;
	this.sourceView = this.options.sourceView;
	this.destinationView = this.options.destinationView;

	this.listenTo(this.sourceView, 'dom:refresh', this.attachSourceHandlers);
    },
    
    attachSourceHandlers: function(){
	console.log('in attachSourceHandlers');
    	//We have to track for which leaf element was selected when dragging starts
	this.sourceView.children.each(function(view){
	    view.children.each(function(leaf){
		leaf.$el.bind('dragstart', 
		    _.bind(function(event, ui){
			console.log('dragging started');
			this.dragView = leaf;
			this.dragSource = 'sourceView';
		    }, this)
		);
	    }, this);
	}, this);
    },
    
    attachDestinationHandlers: function(){
	console.log('in attachDestinationHandlers');
       $('div.menu-content > ul').bind('sortstart', _.bind(function(event, ui){	    //@TODO: move selector as option
	    if(this.dragSource)
		return;	//Dragging from source have been already started
	    
	    console.log('sorting started');
	    
	    this.dragView = ui.item.data('view');
	    this.dragSource = 'destinationView';
       },this));
    },
    
    // Iterates over sourceView's collection and checks whether the item
    // exists in the destinationView's collection. If so, prevents the item
    // from dragging
    updateDraggingAvailability: function(){
	console.log('updating dragging availability');
	this.sourceView.$el.find('.unavailable').removeClass('unavailable');
	
	this.sourceView.children.each(function(view){
	    view.children.each(function(leaf){
		if(this.destinationView.collection.where({reportId: leaf.model.get('id')}, {deep: true}).length){   //@TODO: Remove dependency
		    leaf.$el.addClass('unavailable');
		}
	    }, this);
	}, this);	
    },
    
    initDragDrop: function(){
	console.log('In initDragDrop');
	
	//Adds reference for existing DOM object to the Backbone.View
	this.destinationView.children.each(function(view){
	    view.$el.data('view', view);
	    view.children.each(function(leaf){
		leaf.$el.data('view', leaf);
	    });
	});
	
	this.attachDestinationHandlers();
	
	this.updateDraggingAvailability();
	
	this.$sortable = $( 'div.menu-content > ul' ).sortable({	//@TODO: move selector to options
	    placeholder: 'ui-state-highlight',
	    forcePlaceholderSize: true,
	    items: '> li > ul > li',
	    cancel: 'li.no-drag',
	    beforeStop: _.bind(function(event, ui){
		console.log('before stop');
		
		//Adding a new page to the menu
		var $item = $(ui.item), 
		    options = {};
		
		//Fix the case when user drags new page to the end of the list
		if($item.parent('ul.ui-sortable').length === 1){
		    //get the topest ul
		    $parentLi = $item.parent().find('li:has(ul)').last();
		    $item.remove();
		}else{
		    $parentLi = $item.parent().closest('li');
		    options.at = $item.closest('ul').children().index($item);
		}
		
		var parentView = $parentLi.data('view');
		
		if(!this.isRemoving && this.dragSource === 'destinationView'){ 
		    // Resorting menu item, so remove from previous parent 
		    var parentCollection = this.dragView.model.parent().nodes();
		    parentCollection.remove(this.dragView.model);
		    
		    parentView.model.add(_.clone(this.dragView.model.attributes), options);
		    this.destinationView.collection.trigger('menugroupchanged');
		}else if(this.dragSource === 'sourceView'){
		    //Adding new page from pages source
		    var model = {
			title: this.dragView.model.get('title'),
			report: _.clone(this.dragView.model.attributes)
		    };
		    parentView.model.add(model, options);
		    this.destinationView.collection.trigger('menugroupchanged');
		}
		
		this.dragSource = false;
		this.dragView = undefined;
		this.isRemoving = false;
	    }, this)
	});
	
	this.$draggable = $( this.draggableSelector ).draggable({
	    connectToSortable: this.$sortable,
	    helper: 'clone',
	    revert: 'invalid',
	    cancel: '.unavailable',
	    containment: 'section.content.content-internal'
	});

	// Setting up the droppable region which will act as trash bin
	// @TODO: move up customization property for it
	this.$droppable = $('div.pages-column').droppable({
	    accept: 'ul.ui-sortable li',
	    activeClass: 'droppable-active',
	    drop: _.bind(function( event, ui ) {
		//Delete from menu
		console.log('Something dropped somewhere! ' + ui.draggable);
		var collection = this.dragView.model.parent().nodes();
		collection.remove(this.dragView.model);

//		this.dragView.model.remove();
		
		this.destinationView.collection.trigger('menuitemremoved');
		this.updateDraggingAvailability();
	    }, this),
	    over: _.bind(function(event, ui){
		this.isRemoving = true;
	
		$(ui.draggable).append('<i class="icon-trash"></i>');
	    }, this),
	    out: _.bind(function(event, ui){
		$(ui.draggable).find('i.icon-trash').remove();
	    }, this),
	});
    },
    
    onClose: function(){
	console.log('In onClose');
	
	if(this.$sortable || this.$droppable || this.$draggable){
	    
	    //Killing all references to listened objects
	    this.sourceView.off('dom:refresh', this.initDragDrop);
	    this.destinationView.off('dom:refresh', this.initDragDrop);

	    //Killing sortable, draggable, droppable
	    this.$droppable.destroy();
	    this.$draggable.destroy();
	    this.$sortable.destroy();
	    
	    this.$droppable = this.$draggable = this.$sortable = undefined;
	    
	    this.isDestinationReady = this.isSourceReady = false;
	}
    },
    
    render: function(){
	// It does not render any elements, just returns self for compability
	return this;
    }
});
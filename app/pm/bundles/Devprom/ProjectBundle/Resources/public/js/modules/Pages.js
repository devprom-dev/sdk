/*
 * The Pages module displays list of pages available for menu configuration with
 * filtration ability.
 * 
 * @author Vasiliy Pedak truvazia@gmail.com
 */
var App = App || {};

App.module("Pages", function(Pages, App, Backbone, Marionette, $, _){
    
    this.pagesCollection = new ReportCategory();
    
    var 
	//Filterable item that will be showed in the available pages list
	PageItemView = FilterableTreeNodeView.extend(
	{
	    filter: $('form.filter input'),
	    filterAttributes: [
	           {attribute:'title', container:'div.hdr'},
	           {attribute:'desc', container:'div.desc'}
	    ],
	    template: '#page-list-node-template',

	    itemViewOptions: function(){
		return {
		    treeView: this.treeView
		};
	    },

	    initialize: function(options){
		if(options.treeView){
		    this.treeView = options.treeView;
		}

		FilterableTreeNodeView.prototype.initialize.apply(this, arguments);
	    },

	    onMatch: function(){
		if(!this.model.nodes()){
		    var groupView = this.treeView.children.findByModel(this.model.parent());
		    groupView.trigger('childMatched');
		}
	    },

	    onNotMatch: function(){
		if(this.treeView){
		    this.on('childMatched', function(){
			this.$el.removeClass(this.notMatchClass);
			this.off('childMatched');
		    });
		}
	    }
	}),
	
	//A list of available pages
	PagesView = TreeView.extend({
	    collection: Pages.pagesCollection,
	    itemView: PageItemView,

	    itemViewOptions: function(){
		return {
		    treeView: this
		};
	    }, 

	    onRender: function(){
	       this.initFilter();
	    },
		    
	    //Providing ability to filter pages by typing keys
	    //@TODO: move this functionality to FilterInput class
	    initFilter: function(){
	       var filterOnTyping = true,
		   resetOnFocusOut = false;
	       
	       if(!filterOnTyping){
		   $('form.filter input').on('keypress', function(event){
			if(event.keyCode === 13){
			    console.log('enter pressed');
			    event.preventDefault();
			    $(this).trigger('change');
			}
		   });
	       }else if(resetOnFocusOut){
		   //resets on focusout
		   $('form.filter input').focusout(function(){
			$(this).val('');
			$(this).trigger('change');
		   });
	       }
	       
	       $('form.filter input').on('keyup', function(event){
		    //resets on esc
		    if(event.keyCode === 27){
			$(this).val('');
			$(this).trigger('change');	
		    }else if(filterOnTyping){
			$(this).trigger('change');
		    }
			});
		}
	});
    
    Pages.pagesView = new PagesView({});
});


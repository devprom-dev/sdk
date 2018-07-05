var FilterableTreeNodeView = TreeNodeView.extend({
    ui: {},
    
    /**
     * @property {String} filter Filter input selector
     */
    filter: '',
    
    /**
     * @property array of 
     * {
     * 		attribute:{string}, // attribute of the model with text is used for filtering 
     * 		container:{string}	// selector which contains attribute value
     * }
     */
    filterAttributes: [],
    
    // This classes will be added to the tree's node which was processed by the filtrate method
    notMatchClass: 'filter-not-match',
    matchClass: 'filter-match',
    
    matchTemplate: _.template('<span class="<%= matchClass %>"><%= matchedText %></span>'),

    initialize: function()
    {
		RegExp.escape = function(text) {
			return text.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&");
		};

		if(this.filter instanceof String)
		    this.ui.filter = $(this.filter);
		else
		    this.ui.filter = this.filter;
	
		var self = this;
		
		this.ui.filter.on('change', function(){
		    var currentValue = $(this).val();
		    self.filtrate(currentValue);
		});
	
		TreeNodeView.prototype.initialize.apply(this, arguments);
    },
    /**
     * If model's attribute of the current item contains a current filter value
     * then the item will be rerendered with highligting of the match, else it will be
     * hidden
     * 
     * @param {String} filterValue	Current value of the filter
     */
    filtrate: function(filterValue)
    {
		if ( filterValue == '' ) {
		    this.reset();
		    return;
		}
		
		var is_matched = false;
		var regExp = new RegExp('(' + RegExp.escape(filterValue) +')','i');
		var tree = this;
		
		$.each(this.filterAttributes, function(index, item)
		{
			if( (matches = regExp.exec(tree.model.get(item.attribute))) !== null)
			{
				tree.reset(item.attribute, item.container);
				tree.match(matches, item.container, tree.model.get(item.attribute), filterValue);
			    
			    is_matched = true;
			}
		});
		
		if ( !is_matched ) this.notMatch();
    },
	    
    reset: function(attribute, container)
    {
		this.$el.find(container).first().html(this.model.get(attribute));
		this.$el.removeClass(this.notMatchClass);
    },
	    
    match: function(matches, container, source, filterValue)
    {
		var match = new RegExp('(' + RegExp.escape(filterValue) +')','i');
		source = source.replace(match, this.matchTemplate({matchClass: this.matchClass, matchedText: matches[0]}));
		this.$el.find(container).first().html(source);
		this.onMatch(matches, source, filterValue);
    },
	    
    notMatch: function()
    {
		//hiding the item
		this.$el.addClass(this.notMatchClass);
		this.onNotMatch();
    },
    
    // Can be overloaded for customization
    onMatch: function(matches){},
    onNotMatch: function(){}
});


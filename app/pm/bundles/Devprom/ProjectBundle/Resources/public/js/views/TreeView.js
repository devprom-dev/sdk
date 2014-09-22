/**
 * Displays tree structure uses TreeModel and TreeCollection for it
 */
var TreeNodeView = Backbone.Marionette.CompositeView.extend({
    template: "#node-template",

    tagName: "li",

    initialize: function() {
        // grab the child collection from the parent model
        // so that we can render the collection as children
        // of this parent node
        this.collection = this.model.nodes();
    },

    appendHtml: function(collectionView, itemView) {
        // ensure we nest the child list inside of 
        // the current list item
        collectionView.$("ul:first").append(itemView.el);
    },

    //Removing unused <ul></ul> when current node has no sub-nodes
    onRender: function(){
	if(_.isNull(this.collection)){
            this.$("ul:first").remove();
        }
    }
});

// The tree's root: a simple collection view that renders 
// a recursive tree structure for each item in the collection
var TreeView = Backbone.Marionette.CollectionView.extend({
    tagName: "ul",
    itemView: TreeNodeView
});
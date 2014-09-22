/* 
 * A menu group item
 * 
 * @author Vasiliy Pedak truvazia@gmail.com
 */
var MenuGroupItem = Backbone.TreeModel.extend({
   defaults:{
       id: null,
       title: ''
   },
   /**
    * Overrides TreeCollection's constructor to provide correct children's model
    * 
    * @TODO: Enhance TreeCollection to provide ability of customazing of the children model
    */
   constructor: function tree(node) {
	Backbone.Model.prototype.constructor.apply(this, arguments);
	this._nodes = new (Backbone.TreeCollection.extend({
	    model: MenuItem.create
	}))();
	this._nodes.parent = this;
	if(node && node.nodes) 
	    this.add(node.nodes);
	
//	this.unset('nodes', {silent: true});
   },
	   
   toJSON: function(){
	var jsonObj = Backbone.Model.prototype.toJSON.apply(this, arguments);
	var children = this._nodes.toJSON();
	if(children.length) 
	    jsonObj.nodes = children;
	else if(jsonObj.nodes)
	    delete jsonObj.nodes;
	    
	return jsonObj;
   }
});


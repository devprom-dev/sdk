/* 
 * A clickable menu item model
 * 
 * @author Vasiliy Pedak truvazia@gmail.com
 */
var MenuItem = Backbone.TreeModel.extend({
   defaults:{
       id: null,
       title: '',
       reportId: null,
       report: {}
   },		
	   
   initialize: function(){
       this.on('add change:report', _.bind(function(){
	   this.set('reportId', this.get('report').id);
       },this));
   }
},{
    /**
     * Factory static method
     */
    create: function(attrs, options){
	if(!attrs.report){
	    return new MenuGroupItem(attrs, options);
	}
	
	return new MenuItem(attrs, options);
    }
});


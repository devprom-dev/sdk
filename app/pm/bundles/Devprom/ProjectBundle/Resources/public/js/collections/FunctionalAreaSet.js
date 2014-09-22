/* 
 * @author Vasiliy Pedak truvazia@gmail.com
 */
var FunctionalAreaSet = Backbone.Collection.extend({
	url: function() { return App.module("MenuConfigurator").getRestUrl() + '/functionalareas'; },
    model: FunctionalArea
});

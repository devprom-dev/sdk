/* 
 * @author Vasiliy Pedak truvazia@gmail.com
 */
var ReportCategory = Backbone.TreeCollection.extend({
	url: function() { return App.module("MenuConfigurator").getRestUrl() + '/pages'; },
    model: Report
});


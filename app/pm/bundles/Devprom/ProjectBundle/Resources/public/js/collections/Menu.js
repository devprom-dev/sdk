/* 
 * The menu collection
 * 
 * @author Vasiliy Pedak truvazia@gmail.com
 */
var Menu = Backbone.TreeCollection.extend({
    url: '/menuNodes',
    model: MenuItem.create
});


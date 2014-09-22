/*
 * A available page model
 *   
 * @author Vasiliy Pedak truvazia@gmail.com
 */
var Report = Backbone.TreeModel.extend({
   defaults: {
       id: null,
       title: '',
       type: '',
       desc: '',
       reportUrl: ''
   } 
});


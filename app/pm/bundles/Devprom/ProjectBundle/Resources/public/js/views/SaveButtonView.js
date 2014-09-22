/*
 * Listen to the specified events of objects that can be saved. If any changes found
 * the SaveButton become active. It will disable itself when saving started, and disable
 * views that depends from saving object.
 * 
 * Note: It depends from blindover jquery plugin ( /js/libs/jquery-blindover.js ) 
 * 
 * @param changesSource
 * @param changeEvents
 * 
 * @author Vasiliy Pedak truvazia@gmail.com
 */
var SaveButtonView = Marionette.View.extend(
{
    template: function(serializedModel){ 
		return _.template(
		    '<button class="btn btn-success" disabled="disabled"><span class="message"><%= buttonText %></span> <span class="loader"></span></button>',
		    serializedModel
		);
    },
	    
    ui: {
       button: 'button',
       buttonText: 'button > span.message',
       loader: 'span.loader'
    },
	    
    attributes: {
       "class": 'save-button-view'
    },
	    
    events:{
	"click button": "startSave"
    },
	    
    initialize: function(options){
		//overriding default template if needed
		if(options.template){
		    this.template = options.template;
		}
	    
		_.extend(this, _.pick(options, [
		    'changesSource', 'changeEvents', 'savingObject', 'savingButtonText',
		    'maskSelector'
		]));
		
		this.savingButtonText = this.savingButtonText || t('Saving...');
		
		if(!this.savingObject)
		    this.savingObject = this.changesSource;
		
		if(!this.buttonText)
		    this.buttonText = t('Save');
	
		this.listenTo(this.changesSource, this.changeEvents, this.canSave);
    },
	    
    canSave: function(){
       this.ui.button.prop('disabled', false);
    },
	    
    cantSave: function(){
       this.ui.button.prop('disabled', true);
    },
	    
    startSave: function(){
	//Applying mask to specified elements
	if(this.maskSelector){
	    $(this.maskSelector).blindOver().css('cursor', 'wait');
	}
	
	this.cantSave();
	this.ui.buttonText.text(this.savingButtonText);
	this.ui.loader.show();
	this.onStartSave();
	this.trigger('savingstarted');
    },
	    
    saved: function(){
	this.reset();
	this.trigger('savingdone');
    },

    unMask: function(){
	if(this.maskSelector){
	    $(this.maskSelector).find('div.blindover').remove();
	    $(this.maskSelector).removeClass('blinded').css('cursor', '');
	}
    },

    reset: function(){
	this.ui.buttonText.text(this.buttonText);
	this.cantSave();
	this.unMask();
    },
    
    render: function(){
	this.$el.html(
	    this.template({
		buttonText: this.buttonText
	    })
	);
	    
	this.bindUIElements();
	return this;
    },
    
    //Available for customizations in the subclasses
    // @TODO: provide default logic for model here - when 'sync' triggered reset the button
    onStartSave: function(){}
});


/* 
 * InlineAdder can be used for adding new models to collections that was displayed 
 * as lists or trees.
 * 
 * @TODO: For edition of an adding model attribute it is better to use an InlineEditor class. So we should create it later
 * 
 * @author Vasiliy Pedak truvazia@gmail.com
 */
var InlineAdder = Backbone.Marionette.View.extend({

    /**
     * @TODO: - Allow to chose template from ui instead this hardcoded
     */
    inputTemplate: _.template('<div class="row-fluid"><div class="input-append span7"><input id="appendedInput" class="span12" type="text" placeholder="Название группы" value="<%= propertyValue %>"><span class="add-on"><i class="key-enter-icon"></i></span></div></div>'),
    buttonTemplate: _.template('<div class="for-fluid"><button class="btn span7"><i class="icon-plus"></i>&nbsp;<%= text %></button></div>'),
    
    ui:{
	button: "button",
	input: "input"
    },
    
    events:{
	"click button": function(){
	    this.ui.input = this.$el.prepend(
		    $(this.inputTemplate({propertyValue: this.options.inputPropertyDefaul || ''}))
	    ).find('input');
	    
	    this.ui.button.prop("disabled", true);
	    this.ui.input.focus();
	    
	    this.ui.input.blur(_.bind(function(){
		if(this.ui.input.val() && confirm('Сохранить изменения?')){
		    this.add();
		}else
		    this.closeInput();
	    }, this));
	},
	"keydown input": "keydown"
    },
    
    render: function(){
	var text = this.options.buttonText || 'Add';
	this.$el.append($(this.buttonTemplate({"text": text})));
	this.bindUIElements();
	return this;
    },
    
    closeInput: function(){
	this.ui.input.off('blur');
	this.ui.input.parent().remove();
	this.ui.button.prop("disabled", false);
    },
    
    keydown: function(event){
	if(event.keyCode === 27){
	    this.closeInput();
	}
	
	if(event.keyCode === 13){
	    this.add();
	}
    },
    
    add: function(){
	var attributes = {},
	    inputVal = this.ui.input.val(),
	    model;
	
	if(inputVal === ''){
	    this.closeInput();
	    return;
	}
	
	attributes[this.options.property] = inputVal;
	model = new this.collection.model(attributes);
	
	if(model.validate){
	    /*
	     * @TODO All messages must be selected from App.TranslationManager.translate(messageId || message);
	     */
	    var message = 'Невозможно выполнить операцию. ';
	    if(model.validate() === false){
		message += model.vaidationError || '';
		/**
		 * @TODO Nice error messages needed instead of alert 
		 */
		alert(message);
		return; 
	    }
	}
	
	this.collection.add(model);
	this.closeInput();
    }
});



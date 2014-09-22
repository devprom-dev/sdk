/* 
 * Provides specific data and logic for menu's group item. 
 * 
 * @TODO: 
 *  - It should use an InlineEditor class instance for edit functionality, after InlineEditor will be realized.
 *  
 * @author Vasiliy Pedak truvazia@gmail.com
 */
var GroupMenuNodeView = TreeNodeView.extend({
    itemView: TreeNodeView,
    template: "#menu-group-node",
    
    ui:{
	span: 'div',
	spanWithTitle: 'div > span',
	input: 'input',
	controls: 'i.controls'
    },
    
    events: {
	"dblclick > div": "showInput",
	"keydown input": "dispatchKeys",
	"mouseover > div": "showControlBtns",
	"mouseout > div": "hideControlBtns",
	"click i.icon-remove": "throwOut",
	"click i.icon-edit": "showInput"
    },
    
    showInput: function(){
	if(this.$el.find('input').length)
	    return;
	
	this.ui.span.hide();
	this.$el.prepend('<div class="input-append"><input id="appendedInput" class="span12" type="text">'
			    + '<span class="add-on"><i class="key-enter-icon"></i></span></div>');
	this.ui.inputContainer = this.$el.find('div.input-append');
	this.ui.input = this.ui.inputContainer.find('input').val(this.model.get('title'));;
	this.ui.input.focus();
	
	this.ui.input.blur(_.bind(function(){
	    var inputValue = this.ui.input.val();
	    if(inputValue !== this.model.get('title')){
		if(confirm('Сохранить изменения?')){
		    this.update();
		}else{
		    this.closeInput();
		}
	    }
	    
	}, this));
    },
	    
    closeInput: function(){
	this.ui.input.off('blur');
	this.ui.inputContainer.remove();
	this.ui.spanWithTitle.text(this.model.get('title'));
	this.ui.span.show();
    },	
    	    
    showControlBtns: function(){
	this.ui.controls.show();
    },
	    
    hideControlBtns: function(){
	this.ui.controls.hide();
    },
	    
    dispatchKeys: function(event){
	console.log('in dispatch key');
	if(event.keyCode === 13){
	    this.update();
	}else if(event.keyCode === 27){
	    this.closeInput();
	}
    },
	    
    update: function(event){
	//Prevent second call of this method in the case when user had deleted
	//input's value and pressed ENTER. He will be asked for confirmation, so if
	//he will click on the OK, the focusout event will be triggered
	if(this.updating)
	    return; 
	this.updating = true;
	
	//update model
	var inputValue = this.ui.input.val();

	if(inputValue === '' && this.model.get('title')){
	    //Model is deleting
	    this.throwOut();
	}else{
	    this.model.set('title', this.ui.input.val());
	    this.model.collection.trigger('menuGroupItemEdited');
	    this.closeInput();
	}
	
	this.updating = false;
    },
	    
    throwOut: function(){
	if(confirm("Вы уверены что хотите удалить группу?")){
	    var menuNodesCollection = this.model.collection;
	    menuNodesCollection.remove(this.model);
	    menuNodesCollection.trigger('menugroupchanged');
	}else{
	    this.ui.input.val(this.model.get('title'));
	}	
    },
	    
    onRender: function(){
	if(!this.model.parent() && !this.model.nodes()){
	    this.$el.find('ul').append('<li class="no-drag"></li>');
	}else{
	    TreeNodeView.prototype.onRender.apply(this, arguments);
	}
    }
    
});


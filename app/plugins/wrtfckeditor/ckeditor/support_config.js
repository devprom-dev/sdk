/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.disableAutoInline = true;

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For the complete reference:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	config.extraPlugins = 'imagemulti,lineutils';
	config.removePlugins = 'autoembed,undo,mathjax';
	config.allowedContent = true;
	config.embed_provider = '//ckeditor.iframe.ly/api/oembed?url={url}&callback={callback}';
	config.autoEmbed_widget = 'customEmbed';
	config.disableNativeSpellChecker = false;
	config.toolbar_MiniToolbar = [
		{ name: 'clipboard', items : [ 'Cut','Copy','Paste' ] },
		{ name: 'editing', items : [ 'Scayt' ] },
		{ name: 'insert', items : [ 'InsertMultipleImages','Table','Link','HorizontalRule','Smiley','CodeSnippet','Maximize' ] },
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent' ] },
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','-','RemoveFormat' ] },
		{ name: 'colors', items : [ 'TextColor','BGColor' ] }
	];
	config.removeDialogTabs = 'image2:advanced';
	config.linkShowAdvancedTab = false;
	config.linkShowTargetTab = false;
};

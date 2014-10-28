/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.disableAutoInline = true;

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For the complete reference:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

	config.extraPlugins = 'plantuml,lineutils';
	
	config.allowedContent = true;
	config.disableNativeSpellChecker = false;
	config.entities = false;

	if ( devpromOpts.mathJaxLib != '' ) config.mathJaxLib = devpromOpts.mathJaxLib;
	config.plantUMLServer = devpromOpts.plantUMLServer != '' ? devpromOpts.plantUMLServer : 'http://www.plantuml.com';

	config.toolbar_FullToolbar =
	[
		{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo', '-', 'TextColor','BGColor' ] },
		{ name: 'editing', items : [ 'Scayt' ] },
		{ name: 'insert', items : [ 'Image','Table','Link','HorizontalRule','Smiley','SpecialChar', 'Plantuml', 'Mathjax', 'EqnEditor','CodeSnippet' ] },
		{ name: 'tools', items : [ 'Maximize', 'ShowBlocks' ] },
		{ name: 'forms', items : [ 'Checkbox' ] },
		{ name: 'document', items : [ 'Source' ] },
		'/',
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','-','Subscript','Superscript','-','RemoveFormat' ] },
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','Blockquote' ] },
		{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] }
	];

	config.toolbar_MiniToolbar =
	[
		{ name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord' ] },
		{ name: 'editing', items : [ 'Scayt' ] },
		{ name: 'insert', items : [ 'Image','Table','Link','HorizontalRule','Smiley','SpecialChar', 'Plantuml', 'Mathjax', 'EqnEditor','CodeSnippet','Maximize' ] },
		{ name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ] },
		'/',
		{ name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','-','Blockquote','-','RemoveFormat' ] },
		{ name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
		{ name: 'colors', items : [ 'TextColor','BGColor' ] }
	];

	config.removeDialogTabs = 'image:advanced;link:advanced';
};

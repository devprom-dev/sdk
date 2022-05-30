/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.disableAutoInline = true;
CKEDITOR.verbosity = CKEDITOR.VERBOSITY_ERROR;

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here.
	// For the complete reference:
	// http://docs.ckeditor.com/#!/api/CKEDITOR.config

    config.extraPlugins = 'clipboard,toolbar,tableresize2,undo,includepage,imagemulti,linkex,embedhtml,plantuml,diagrams,texttemplates,searchartifacts,includeartifacts,productivity' + devpromOpts.extraPlugins;
    config.embed_provider = '//ckeditor.iframe.ly/api/oembed?url={url}&callback={callback}';
    config.autoEmbed_widget = 'customEmbed';
	config.allowedContent = true;
	config.extraAllowedContent = 'svg mark';
	config.removeFormatTags = '';
	config.disableNativeSpellChecker = false;
	config.entities = false;
    config.debug = false;
    config.autoGrow_maxHeight = $(window).height() * 0.5;
    config.autoGrow_onStartup = true;
	config.mathJaxLib = devpromOpts.mathJaxLib != ''
		? devpromOpts.mathJaxLib : window.location.protocol + "//cdnjs.cloudflare.com/ajax/libs/mathjax/2.2.0/MathJax.js?config=TeX-AMS_HTML";
	
	config.plantUMLServer = devpromOpts.plantUMLServer != '' ? devpromOpts.plantUMLServer : 'http://www.plantuml.com';
	config.removeDialogTabs = 'image2:advanced';
	config.linkShowAdvancedTab = false;
	config.linkShowTargetTab = false;
	config.codeSnippet_languages = {ones:"1C",apache:"Apache",bash:"Bash",coffeescript:"CoffeeScript",cpp:"C++",cs:"C#",css:"CSS",diff:"Diff",html:"HTML",http:"HTTP",ini:"INI",java:"Java",javascript:"JavaScript",json:"JSON",lua:"Lua",makefile:"Makefile",markdown:"Markdown",nginx:"Nginx",objectivec:"Objective-C",perl:"Perl",php:"PHP",python:"Python",ruby:"Ruby",sql:"SQL",vbscript:"VBScript",xhtml:"XHTML",xml:"XML"};
    config.codeSnippet_theme = 'github';
	config.undoStackSize = 35;
	config.skin = 'moono-lisa'
};

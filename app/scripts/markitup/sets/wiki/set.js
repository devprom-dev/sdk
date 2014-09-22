// -------------------------------------------------------------------
// markItUp!
// -------------------------------------------------------------------
// Copyright (C) 2008 Jay Salvat
// http://markitup.jaysalvat.com/
// -------------------------------------------------------------------
// Mediawiki Wiki tags example
// -------------------------------------------------------------------
// Feel free to add more tags
// -------------------------------------------------------------------
mySettings = {
    resizeHandle: false,
	previewInWindow: 'width=800, height=600, resizable=yes, scrollbars=yes',
	previewParserPath:	'/cms/preview.php', // path to your Wiki parser
	previewParserVar: 'content',
	onShiftEnter:		{keepDefault:false, replaceWith:'\n\n'},
	markupSet: [
		{name:'Heading 1', key:'1', openWith:'h1 ', closeWith:'' },
		{name:'Heading 2', key:'2', openWith:'h2 ', closeWith:'' },
		{name:'Heading 3', key:'3', openWith:'h3 ', closeWith:'' },
		{name:'Heading 4', key:'4', openWith:'h4', closeWith:'' },
		{name:'Heading 5', key:'5', openWith:'h5', closeWith:'' },
		{separator:'---------------' },		
		{name:'Bold', key:'B', openWith:"*", closeWith:"*"}, 
		{name:'Italic', key:'I', openWith:"_", closeWith:"_"}, 
		{name:'Stroke through', key:'S', openWith:'--', closeWith:'--'}, 
		{separator:'---------------' },
		{name:'Bulleted list', openWith:'(!(* |!|*)!)'}, 
		{name:'Numeric list', openWith:'(!(# |!|#)!)'}, 
		{separator:'---------------' },
		{name:'Picture', key:"P", replaceWith:'[[Image:[![Url:!:http://]!]|[![name]!]]]'}, 
		{name:'Link', key:"L", openWith:"[[![Link]!] ", closeWith:']', placeHolder:'Your text to link here...' },
		{name:'Url', openWith:"[[![Url:!:http://]!] ", closeWith:']', placeHolder:'Your text to link here...' },
		{separator:'---------------' },
		{name:'Quotes', openWith:'[note=', closeWith:']', placeHolder:''},
		{name:'Code', openWith:'[code]', closeWith:'[/code]'}, 
		{separator:'---------------' },
		{name:'Preview', call:'preview', className:'preview'}
	]
}
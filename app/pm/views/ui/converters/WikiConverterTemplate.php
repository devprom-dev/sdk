<?php

include_once "WikiConverterPreview.php";

class WikiConverterTemplate extends WikiConverterPreview
{
 	var $template_it;
 	
 	function WikiConverterTemplate( $template ) 
 	{
 		global $_REQUEST, $model_factory;

 		$templ_cls = $model_factory->getObject('TemplateHTML');
 		$this->template_it = $templ_cls->getExact($template);
 	}
 	
 	function drawCSS() 
 	{
 		if ( $this->template_it->count() > 0 )
 		{
 			echo '<style>'.$this->template_it->getHtmlDecoded('CSSBlock').'</style>';
 		}
 		else
 		{
 			echo '<link rel="stylesheet" type="text/css" href="/cache?type=css">'.
 				'<style> body {background:white;line-height:1.5;} .introduction, .body {margin:12px;} .section{margin:16px;} .text{margin:16px;} .content {margin-top:16px;margin-left:4px;} HR{page-break-after: always;} </style>';
 		}
 	}
	
	function drawHeader() 
	{
 		if ( $this->template_it->count() > 0 )
 		{
 			echo $this->template_it->getHtmlDecoded('Header');
 		}
	}
	
	function drawFooter() 
	{
 		if ( $this->template_it->count() > 0 )
 		{
 			echo $this->template_it->getHtmlDecoded('Footer');
 		}
	}
 	
 	function HasContents() 
 	{
 		if ( $this->template_it->count() > 0 )
 		{
 			return $this->template_it->get('HeaderContents') == 'Y';
 		}
 		else
 		{
 			return true;
 		}
 	}
 	
 	function HasNumberedSections() 
 	{
 		return $this->template_it->get('SectionNumbers') == 'Y';
 	}

	function drawSeparator()
	{
		if ( $this->template_it->count() < 1 )
		{
			echo '<br/>';
			echo '<hr/>';
		}
	}
}

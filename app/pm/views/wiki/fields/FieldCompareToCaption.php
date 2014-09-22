<?php

include_once SERVER_ROOT_PATH."ext/htmldiff/html_diff.php";

class FieldCompareToCaption extends FieldStatic
{
	private $page_it;
	
	private $compare_to_page_it;
	
	public function __construct( $page_it, $compare_to_page_it )
	{
		$this->page_it = $page_it;
		
		$this->compare_to_page_it = $compare_to_page_it;
	}
	
	function draw()
	{
		$editor = WikiEditorBuilder::build($this->page_it->get('ContentEditor'));
		
		echo '<div class="reset wysiwyg">';
		
			$parser = $editor->getHtmlParser();

	 		echo IteratorBase::utf8towin(
	 				html_diff(
	 						$this->compare_to_page_it->getId() > 0
	 							? IteratorBase::wintoutf8($parser->parse($this->compare_to_page_it->getHtmlDecoded('Caption')))
	 							: "", 
 							IteratorBase::wintoutf8($parser->parse($this->page_it->getHtmlDecoded('Caption')))
					)
	 		);  

	 	echo '</div>';
	}
}
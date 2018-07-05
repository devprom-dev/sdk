<?php
include_once SERVER_ROOT_PATH . "pm/views/wiki/diff/WikiHtmlDiff.php";

class FieldCompareToContent extends FieldStatic
{
	private $page_it;
	private $compare_to_page_it;
	
	public function __construct( $page_it, $compare_to_page_it ) {
		$this->page_it = $page_it;
		$this->compare_to_page_it = $compare_to_page_it;
	}

	function setSearchText($text) {
	}
	
	function draw( $view = null )
	{
		$editor = WikiEditorBuilder::build($this->page_it->get('ContentEditor'));
		$parser = $editor->getComparerParser();
        $parser->setObjectIt($this->page_it);

		echo '<div class="reset wysiwyg">';
	 		$diffBuilder = new WikiHtmlDiff(
 						$this->compare_to_page_it->getId() > 0
 							? $parser->parse($this->compare_to_page_it->getHtmlDecoded('Content'))
 							: "", 
	 					$parser->parse($this->page_it->getHtmlDecoded('Content'))
			);
			echo $diffBuilder->build();
	 	echo '</div>';
	}
}
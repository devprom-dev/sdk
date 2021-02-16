<?php
include_once SERVER_ROOT_PATH . "pm/views/wiki/diff/WikiHtmlDiff.php";

class FieldCompareToContent extends FieldStatic
{
	private $page_it;
	private $selfContent = '';
	private $compareToContent = '';
	
	public function __construct( $page_it, $selfContent, $compareToContent ) {
		$this->page_it = $page_it;
		$this->selfContent = $selfContent;
		$this->compareToContent = $compareToContent;
	}

	function setSearchText($text) {
	}
	
	function draw( $view = null )
	{
		$editor = WikiEditorBuilder::build($this->page_it->get('ContentEditor'));
		$parser = $editor->getComparerParser();
        $parser->setObjectIt($this->page_it->copy());

		echo '<div class="reset wysiwyg">';
	 		$diffBuilder = new WikiHtmlDiff(
            $this->compareToContent != '' ? $parser->parse($this->compareToContent) : "",
                    $parser->parse($this->selfContent)
                );
			echo $diffBuilder->build();
	 	echo '</div>';
	}
}
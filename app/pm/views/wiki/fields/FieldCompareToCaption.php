<?php
include_once SERVER_ROOT_PATH . "pm/views/wiki/diff/WikiHtmlDiff.php";

class FieldCompareToCaption extends FieldStatic
{
	private $page_it;
	private $compare_to_page_it;
	
	public function __construct( $page_it, $compare_to_page_it ) {
		$this->page_it = $page_it;
		$this->compare_to_page_it = $compare_to_page_it;
	}
	
	function draw( $view = null )
	{
		$editor = WikiEditorBuilder::build($this->page_it->get('ContentEditor'));
		$parser = $editor->getHtmlParser();

		$diffBuilder = new WikiHtmlDiff(
			$this->compare_to_page_it->getId() > 0
				? $parser->parse($this->compare_to_page_it->getHtmlDecoded('Caption'))
				: "",
			$parser->parse($this->page_it->getHtmlDecoded('Caption'))
		);

		echo '<div class="reset wysiwyg">';
			echo $diffBuilder->build();
	 	echo '</div>';
	}
}
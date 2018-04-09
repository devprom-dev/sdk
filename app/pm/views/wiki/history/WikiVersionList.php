<?php
include_once SERVER_ROOT_PATH . 'pm/views/wiki/editors/WikiEditorBuilder.php';
include_once SERVER_ROOT_PATH . 'pm/views/wiki/diff/WikiHtmlDiff.php';

class WikiVersionList extends PMStaticPageList
{
    private $curr_content;
    private $lastBaseline;
    private $parser;

    function extendModel()
    {
        parent::extendModel();

        $visible = array('Caption', 'ObjectId', 'RecordCreated');
        foreach( array_keys($this->getObject()->getAttributes()) as $attribute ) {
            $this->getObject()->setAttributeVisible($attribute, in_array($attribute, $visible));
        }
        $this->getObject()->setAttributeCaption('ObjectId', text(2236));
    }

    function retrieve()
	{
		parent::retrieve();

        $page_it = $this->getTable()->getPageIt();
		$this->parser = WikiEditorBuilder::build($page_it->get('ContentEditor'))->getComparerParser();

        foreach( $page_it->fieldToArray('DocumentId') as $documentId ) {
            $this->curr_content[$documentId] = $this->exportHtml($page_it->object->getRegistry()->Query(
                array (
                    new ParentTransitiveFilter($page_it->idsToArray()),
                    new FilterAttributePredicate('DocumentId', $documentId),
                    new SortDocumentClause()
                )
            ));
            $this->lastBaseline[$documentId] = text(2244);
        }
	}

	function getColumnFields() {
		return array (
		    'Caption',
            'ObjectId',
            'RecordCreated'
        );
	}
	
	function getGroupFields() {
		return array('Caption');
	}

	function drawGroup($group_field, $object_it)
    {
        $documentIt = $this->getTable()->getPageIt()->object->getExact($object_it->get('ObjectId'));
        if ( $this->getTable()->getPageIt()->count() == 1 ) {
            $url = $this->getTable()->getPageIt()->getViewUrl();
        }
        else {
            $url = $documentIt->getViewUrl();
        }

        ob_start();
            parent::drawGroup($group_field, $object_it);
            $baseline = ob_get_contents();
        ob_end_clean();

        $text = '<a href="'.$url.'&baseline='.$object_it->getId().'">';
        $text .= $documentIt->getDisplayName() . ' / '.$baseline;
        $text .= '</a>';

        echo '<i class="icon-tasks"></i> &nbsp; ';
        echo preg_replace('/%2/', $this->lastBaseline[$documentIt->getId()], preg_replace('/%1/', $text, text(2243)));

        $this->lastBaseline[$documentIt->getId()] = $baseline;
    }

    function drawCell( $object_it, $attr )
	{
		switch ( $attr )
		{
			case 'ObjectId':
			    $versionText = $this->getVersionText($object_it);
			    $this->parser->setObjectIt($object_it->copy());
		        echo $this->getPagesDiff($versionText, $this->curr_content[$object_it->get('ObjectId')]);
                $this->curr_content[$object_it->get('ObjectId')] = $versionText;
				break;
			default:
				parent::drawCell( $object_it, $attr );
		}
	}

	function getVersionText( $snapshot_it )
    {
        $registry = new ObjectRegistrySQL($this->getTable()->getPageIt()->object);
        return $this->exportHtml($registry->Query(
            array (
                new ParentTransitiveFilter($this->getTable()->getPageIt()->idsToArray()),
                new FilterAttributePredicate('DocumentId', $snapshot_it->get('ObjectId')),
                new SnapshotItemValuePersister($snapshot_it->getId()),
                new DocumentVersionPersister(),
                new SortDocumentClause()
            )
        ));
    }

    function exportHtml( $pageIt )
    {
        ob_start();
        $iteratorObject = new WikiIteratorExportHtml($pageIt);
        $iteratorObject->export();
        $text = ob_get_contents();
        ob_end_clean();
        return $text;
    }

	function getPagesDiff( $prev_content, $curr_content )
	{
		$html = '<div class="reset wysiwyg">';
		$diffBuilder = new WikiHtmlDiff(
			$this->parser->parse($prev_content),
			$this->parser->parse($curr_content)
		);
        $html .= $diffBuilder->build();
		$html .= '</div>';
		return $html;
	}

	function getActions($object_it) {
        return array();
    }
}
 
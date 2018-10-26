<?php
include_once SERVER_ROOT_PATH."cms/classes/ObjectFactoryNotificator.php";
include_once SERVER_ROOT_PATH."ext/diff/finediff.php";

class StoreTextChangesEvent extends ObjectFactoryNotificator
{
    private $session;
    
    function __construct( PMSession $session )
    {
        $this->session = $session;
        parent::__construct();
    }

	function add( $object_it )
	{
		$attribute = $this->getTextAttribute($object_it);
		if ( $attribute == '' ) return;

        if ( $object_it->object instanceof WikiPageChange && $object_it->get('WikiPage') != '' )
        {
            $page_it = getFactory()->getObject('WikiPage')->getRegistryBase()->Query(
                array(
                    new FilterInPredicate($object_it->get('WikiPage'))
                )
            );
            $this->storeChanges($page_it, $object_it->getHtmlDecoded($attribute), $page_it->getHtmlDecoded('Content'));
        }
        else {
            $this->storeChanges($object_it, '', $object_it->getHtmlDecoded($attribute));
        }
	}

	function modify( $prev_object_it, $object_it )
	{
		$attribute = $this->getTextAttribute($object_it);
		if ( $attribute == '' ) return;

        if ( !$object_it->object instanceof WikiPage ) {
            $this->storeChanges($object_it, $prev_object_it->getHtmlDecoded($attribute), $object_it->getHtmlDecoded($attribute));
        }
	}

	function delete( $object_it )
	{
		$attribute = $this->getTextAttribute($object_it);
		if ( $attribute == '' ) return;

		$this->storeChanges($object_it, $object_it->getHtmlDecoded($attribute), "");
	}

	protected function getTextAttribute( $object_it )
	{
		switch( $object_it->object->getEntityRefName() ) {
			case 'pm_ChangeRequest':
				return 'Description';
			case 'WikiPage':
				return 'Content';
            case 'WikiPageChange':
                return 'Content';
			default:
				return '';
		}
	}

	protected function storeChanges( $object_it, $was_text, $now_text )
	{
        $editor = WikiEditorBuilder::build($object_it->get('ContentEditor'));
        $editor->setObjectIt($object_it);
        $parser = $editor->getComparerParser();

        $html2text = new \Html2Text\Html2Text($parser->parse($was_text), array('width'=>0));
        $was_text = preg_replace('/[\r\n]+/', PHP_EOL, $html2text->getText());

        $html2text = new \Html2Text\Html2Text($parser->parse($now_text), array('width'=>0));
        $now_text = preg_replace('/[\r\n]+/', PHP_EOL, $html2text->getText());

		$diff = new FineDiff(
			$was_text,
			$now_text,
			array(
				FineDiff::paragraphDelimiters,
				FineDiff::sentenceDelimiters
			)
		);

		$deleted = 0;
		$inserted = 0;
		$copy_pos = 0;

		foreach( $diff->getOps() as $op ) {
			if ( $op instanceof FineDiffCopyOp ) {
				$copy_pos += $op->getToLen();
			}
			if ( $op instanceof FineDiffDeleteOp ) {
				$deleted += $this->countLines(substr($was_text, $copy_pos, $op->getFromLen()));
			}
			if ( $op instanceof FineDiffInsertOp ) {
				$inserted += $this->countLines($op->getText());
			}
			if ( $op instanceof FineDiffReplaceOp ) {
				$inserted += $this->countLines($op->getText());
			}
		}

		$changes = getFactory()->getObject('pm_TextChanges');
        $changes->setNotificationEnabled(false);
        $changes->add_parms(
			array (
				'ObjectClass' => get_class($object_it->object),
				'ObjectId' => $object_it->getId(),
				'Author' => getSession()->getUserIt()->getId(),
				'Inserted' => $inserted,
				'Deleted' => $deleted,
				'Modified' => $inserted+$deleted,
                'VPD' => $object_it->get('VPD')
			)
		);
	}

    protected function countLines( $text ) {
        return count(
            array_filter(
                preg_split('/[\r\n]+/', trim($text, ' \r\n')), function( $text ) {
                    return trim($text) != '';
        }));
    }
}

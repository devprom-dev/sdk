<?php

class WikiIncludePagePersister extends ObjectSQLPersister
{
	function __construct()
	{
		parent::__construct();
		$this->wikiTrace = getFactory()->getObject('WikiPageTrace');
		$this->issueTrace = getFactory()->getObject('RequestTraceBase');
	}

	public function map( & $parms )
	{
		$ids = array_filter(preg_split('/[,-]/',$parms['PageToInclude']), function($value) {
			return $value > 0;
		});
		if ( count($ids) < 1 ) return "";

		$uid = new ObjectUID;
		$object = $this->getObject();
		$include_it = $object->getRegistry()->Query(
			array (
				new WikiRootTransitiveFilter($ids),
				new FilterVpdPredicate(),
				new SortDocumentClause()
			)
		);

		$ids = $include_it->idsToArray();
		$last_id = array_pop($ids);
		$order_num = $parms['OrderNum'] != '' ? $parms['OrderNum'] : 10;

		if ( count($ids) > 0 )
		{
			$include_it = $object->getRegistry()->Query(
				array (
					new FilterInPredicate($ids),
					new SortDocumentClause()
				)
			);
			$maps = array();
			while( !$include_it->end() ) {
				$id = $object->add_parms(
					array (
						'Caption' => $include_it->getHtmlDecoded('Caption'),
						'Content' => "{{".$uid->getObjectUid($include_it)."}}",
						'IsTemplate' => 0,
						'ParentPage' => $maps[$include_it->get('ParentPage')] != ''
							? $maps[$include_it->get('ParentPage')]
							: $parms['ParentPage'],
						'OrderNum' => $order_num
					)
				);
				$maps[$include_it->getId()] = $id;

				$this->copyTraces($include_it, $id);
				$order_num += 10;
				$include_it->moveNext();
			}
		}

		$include_it = $object->getExact($last_id);
		$parms['Caption'] = $include_it->getHtmlDecoded('Caption');
		$parms['Content'] = "{{".$uid->getObjectUid($include_it)."}}";
		$parms['IsTemplate'] = 0;
		$parms['OrderNum'] = $order_num;
		$parms['ParentPage'] = $maps[$include_it->get('ParentPage')] != ''
									? $maps[$include_it->get('ParentPage')]
									: $parms['ParentPage'];
		$this->include_it = $include_it;
	}

	function add($object_id, $parms)
	{
		if ( is_object($this->include_it) ) {
			$this->copyTraces($this->include_it, $object_id);
		}
	}

	protected function copyTraces( $include_it, $id )
	{
		return; //
		
		$trace_it = $this->wikiTrace->getRegistry()->Query(
			array (
				new FilterAttributePredicate('TargetPage', $include_it->getId())
			)
		);
		while( !$trace_it->end() ) {
			$this->wikiTrace->add_parms(
				array (
					'SourcePage' => $trace_it->get('SourcePage'),
					'TargetPage' => $id
				)
			);
			$trace_it->moveNext();
		}
		$trace_it = $this->issueTrace->getRegistry()->Query(
			array (
				new FilterAttributePredicate('ObjectId', $include_it->getId())
			)
		);
		while( !$trace_it->end() ) {
			$this->issueTrace->add_parms(
				array (
					'ChangeRequest' => $trace_it->get('ChangeRequest'),
					'ObjectClass' => $trace_it->get('ObjectClass'),
					'ObjectId' => $id
				)
			);
			$trace_it->moveNext();
		}
	}
}
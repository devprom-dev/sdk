<?php

include_once SERVER_ROOT_PATH."core/classes/model/validation/ModelValidatorInstance.php";

class ModelValidatorIncludePage extends ModelValidatorInstance
{
	public function validate( Metaobject $object, array & $parms )
	{
		$uid = new ObjectUID;

		$ids = array_filter(preg_split('/[,-]/',$parms['PageToInclude']), function($value) {
			return $value > 0;
		});
		if ( count($ids) < 1 ) return "";

		$include_it = $object->getRegistry()->Query(
			array (
				new WikiRootTransitiveFilter($ids),
				new FilterVpdPredicate(),
				new SortDocumentClause()
			)
		);

		$ids = $include_it->idsToArray();
		$last_id = array_pop($ids);
		$order_num = 10;
		
		if ( count($ids) > 0 )
		{
			$include_it = $object->getRegistry()->Query(
				array (
					new FilterInPredicate($ids),
					new SortDocumentClause()
				)
			);
			$maps = array();
			while( !$include_it->end() )
			{
				$maps[$include_it->getId()] = $object->add_parms(
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
				$order_num += 10;
				$include_it->moveNext();
			}
		}

		$include_it = $object->getExact($last_id);
		$parms['Caption'] = $include_it->getHtmlDecoded('Caption');
		$parms['Content'] = "{{".$uid->getObjectUid($include_it)."}}";
		$parms['IsTemplate'] = 0;
		$parms['ParentPage'] = $maps[$include_it->get('ParentPage')] != ''
				? $maps[$include_it->get('ParentPage')]
				: $parms['ParentPage'];
		$parms['OrderNum'] = $order_num;
		
		return "";
	}
}
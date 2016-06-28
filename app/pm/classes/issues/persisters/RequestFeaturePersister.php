<?php

class RequestFeaturePersister extends ObjectSQLPersister
{
	function map( &$parms )
	{
		if ( $parms['Function'] != '' && !is_numeric($parms['Function']) ) {
			$feature_it = getFactory()->getObject('Feature')->getByRef('Caption', $parms['Function']);
			if ( $feature_it->getId() > 0 ) {
				$parms['Function'] = $feature_it->getId();
			}
			else {
				$parms['Function'] = $feature_it->object->add_parms(
					array (
						'Caption' => $parms['Function']
					)
				);
			}
		}
	}

 	function getSelectColumns( $alias )
 	{
 		return array(
 			" t.Function Features "
 		);
 	}
}


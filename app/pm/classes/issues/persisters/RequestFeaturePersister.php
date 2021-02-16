<?php

class RequestFeaturePersister extends ObjectSQLPersister
{
	function map( &$parms )
	{
		if ( $parms['Function'] != '' && !is_numeric($parms['Function']) ) {
            $matches = array();
		    if ( preg_match('/\[F\-([\d]+)\]/i', $parms['Function'], $matches) ) {
                $feature_it = getFactory()->getObject('Feature')->getExact($matches[1]);
            }
            else {
		        $featureTypeIt = getFactory()->getObject('FeatureType')->getAll();
		        while( !$featureTypeIt->end() ) {
                    $parms['Function'] = str_replace($featureTypeIt->getDisplayName() . ':', '' ,$parms['Function']);
                    $featureTypeIt->moveNext();
                }
                $feature_it = getFactory()->getObject('Feature')->getByRef('Caption', trim($parms['Function']));
            }
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

 	function getSelectColumns( $alias ) {
 		return array();
 	}

 	function IsPersisterImportant() {
        return true;
    }
}


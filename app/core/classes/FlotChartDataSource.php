<?php

class FlotChartDataSource
{
    public static function getData( $it, $aggs )
    {
		$data = array();
		$object = $it->object;

		$agg_values = array();
		if ( count($aggs) > 1 ) {
			$agg_attr = $aggs[1]->getAttribute();
			if ( $object->IsReference($agg_attr) ) {
				$ref = $object->getAttributeObject( $agg_attr );
				if ( $ref->IsDictionary() ) {
					$ref_it = $ref->getAll();
				}
				else {
					$values = $it->fieldToArray($aggs[1]->getAttribute());
					if ( count($values) < 1 ) {
						$ref_it = $ref->getEmptyIterator();
					}
					else {
						$ref_it = $ref->getExact($values);
					}
				}
				while ( !$ref_it->end() )
				{
					$agg_values[$ref_it->getDisplayName()] = 0;
					$ref_it->moveNext();
				}
			}
		}

        $it->moveFirst();
		while ( !$it->end() )
		{
			$attribute = $aggs[0]->getAttribute();
			
			switch ( $attribute )
			{
				default:
					$value = $it->get($attribute);
					if ( $value != '' && $object->IsReference($attribute) )
					{
						$value = $it->getRef($attribute)->getDisplayName();
					}
			}
			
			$value = $it->getWordsOnlyValue( $value, 5 );
			
			if ( trim($value) == '' )
			{
				$value = text(2030);
			}
			
			if ( count($aggs) > 1 )
			{
				$inner_attribute = $aggs[1]->getAttribute();
				
				switch ( $inner_attribute )
				{
					default:
						$inner_value = $it->get($inner_attribute);
						
						if ( $inner_value != '' && $object->IsReference($inner_attribute) )
						{
							$ref_it = $it->getRef($inner_attribute);
							$inner_value = $ref_it->getDisplayName();
						}
				}
				
				$inner_value = $it->getWordsOnlyValue( $inner_value, 5 );
				
                if ( !is_array($data[$value]['data']) )
                {
                    $agg_attr = $aggs[1]->getAttribute();
                    if ( $object->IsReference($agg_attr) ) {
                        $values = $agg_values;
                    }
                    else {
                        $values = array( $inner_value => $it->get($agg_attr) );
                    }

                    $data[$value] =
                        array( 'data' => $values );
                }

                $data[$value]['data'][$inner_value] =
                    round( $it->get($aggs[1]->getAggregateAlias()) );
			}
			else
			{
				$data[$value] =  
					array( 'data' => round($it->get($aggs[0]->getAggregateAlias())) ); 
			}

			$it->moveNext();
		}

		return $data; 		
    }
}
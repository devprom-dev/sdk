<?php

class FlotChartDataSource
{
    public static function getData( $it, $aggs )
    {
		$data = array();

		$object = $it->object;

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
				
				if ( $inner_value != '' )
				{
					if ( !is_array($data[$value]['data']) )
					{
						$agg_attr = $aggs[1]->getAttribute();
						if ( $object->IsReference($agg_attr) )
						{
							$ref = $object->getAttributeObject( $agg_attr );
							
							$ref_it = $ref->getAll();
							$values = array();
							
							while ( !$ref_it->end() )
							{
								$values[$ref_it->getDisplayName()] = 0;
								$ref_it->moveNext();
							}
						}
						else
						{
							$values = array( $inner_value => $it->get($agg_attr) ); 
						}
						 
						$data[$value] =  
							array( 'data' => $values ); 
					}
					
					$data[$value]['data'][$inner_value] = 
						round( $it->get($aggs[1]->getAggregateAlias()) );
				}
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
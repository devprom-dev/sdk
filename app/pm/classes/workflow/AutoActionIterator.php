<?php

class AutoActionIterator extends OrderedIterator
{
	function getConditionXPath()
	{
		$conditions = JsonWrapper::decode($this->getHtmlDecoded('Conditions'));
		if ( !is_array( $conditions['items']) ) return "1=2";
		
		$items = array();
		foreach( $conditions['items'] as $condition )
		{
			$value = addslashes(mb_strtolower($condition['Value']));
			switch($condition['Operator'])
			{
			    case 'is':
			  		$items[] = $condition['Condition'].'="'.$value.'"';
			  		break;

			    case 'isnot':
			  		$items[] = $condition['Condition'].'!="'.$value.'"';
			  		break;

			    case 'contains':
			  		$items[] = 'contains('.$condition['Condition'].',"'.$value.'")';
			  		break;

			    case 'notcontains':
			  		$items[] = 'not(contains('.$condition['Condition'].',"'.$value.'"))';
			  		break;

			    case 'unknown':
			  		$items[] = $condition['Condition'].'=""';
			  		break;

			    case 'any':
			  		$items[] = $condition['Condition'].'!=""';
			  		break;

				case 'less':
					$items[] = $condition['Condition'].'<"'.$value.'"';
					break;

				case 'greater':
					$items[] = $condition['Condition'].'>"'.$value.'"';
					break;
			}
		}
		if ( count($items) < 1 ) return "1=2";
		return join(
				$conditions['mode'] == 'all' ? ' and ' : ' or ',
				$items
			);	
	}
}
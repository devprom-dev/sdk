<?php

class AutoActionIterator extends OrderedIterator
{
	function getConditionXPath( $translate = false )
	{
		$conditions = JsonWrapper::decode($this->getHtmlDecoded('Conditions'));
		if ( !is_array( $conditions['items']) ) return "1=2";

		if ( $translate ) {
		    $subject = getFactory()->getObject($this->object->getSubjectClassName());
        }

		$items = array();
		foreach( $conditions['items'] as $condition )
		{
			$value = addslashes(mb_strtolower($condition['Value']));
			$attributeName = is_object($subject)
                ? $subject->getAttributeUserName($condition['Condition'])
                : $condition['Condition'];

			switch($condition['Operator'])
			{
			    case 'is':
			  		$items[] = $attributeName.'="'.$value.'"';
			  		break;

			    case 'isnot':
			  		$items[] = $attributeName.'!="'.$value.'"';
			  		break;

			    case 'contains':
			  		$items[] = 'contains('.$attributeName.',"'.$value.'")';
			  		break;

			    case 'notcontains':
			  		$items[] = 'not(contains('.$attributeName.',"'.$value.'"))';
			  		break;

			    case 'unknown':
			  		$items[] = $attributeName.'=""';
			  		break;

			    case 'any':
			  		$items[] = $attributeName.'!=""';
			  		break;

				case 'less':
					$items[] = is_numeric($value) ? "{$attributeName} < {$value}" : $attributeName.'<"'.$value.'"';
					break;

				case 'greater':
					$items[] = is_numeric($value) ? "{$attributeName} > {$value}" : $attributeName.'>"'.$value.'"';
					break;
			}
		}
		if ( count($items) < 1 ) return "1=2";
		return join(
				$conditions['mode'] == 'all' ? ' and ' : ' or ',
				$items
			);	
	}

    function getConditionAttributes()
    {
        $conditions = JsonWrapper::decode($this->getHtmlDecoded('Conditions'));
        if ( !is_array( $conditions['items']) ) return array();

        $items = array();
        foreach( $conditions['items'] as $condition ) {
            $items[] = $condition['Condition'];
        }
        return $items;
    }

    function getConditionQueryParms()
    {
        $conditions = JsonWrapper::decode($this->getHtmlDecoded('Conditions'));
        if ( !is_array( $conditions['items']) ) {
            return array( new FilterInPredicate('-123'));
        }

        $items = array();
        $subject = getFactory()->getObject($this->object->getSubjectClassName());

        foreach( $conditions['items'] as $condition )
        {
            $attribute = $condition['Condition'];
            $value = $condition['Value'];

            if ( $attribute == 'State' && $subject instanceof MetaobjectStatable ) {
                $stateIt = \WorkflowScheme::Instance()->getStateIt($subject);
                $stateIt->moveTo('Caption', $value);
                $value = $stateIt->get('ReferenceName');
            }

            switch ( $condition['Operator'] )  {
                case 'is':
                    $items[] = new FilterAttributePredicate($attribute, $value);
                    break;
                case 'isnot':
                    $items[] = new FilterHasNoAttributePredicate($attribute, $value);
                    break;
                case 'contains':
                    $items[] = new FilterSearchAttributesPredicate($value, array($attribute));
                    break;
                case 'unknown':
                    $items[] = new FilterAttributeNullPredicate($attribute);
                    break;
                case 'any':
                    $items[] = new FilterAttributeNotNullPredicate($attribute);
                    break;
                case 'less':
                    $items[] = new FilterAttributeLesserPredicate($attribute, $value);
                    break;
                case 'greater':
                    $items[] = new FilterAttributeGreaterPredicate($attribute, $value);
                    break;
            }
        }

        return $items;
    }
}
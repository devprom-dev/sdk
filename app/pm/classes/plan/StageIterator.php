<?php

class StageIterator extends OrderedIterator
{
 	function getObjectIt()
	{
		global $model_factory;
		
		if ( $this->get('Release') > 0 )
		{
			$object = $model_factory->getObject('Iteration');
			
			return $object->getExact( $this->get('Release') );
		}

		if ( $this->get('Version') > 0 )
		{
			$object = $model_factory->getObject('Release');
			
			return $object->getExact( $this->get('Version') );
		}
	}
	
    function getDisplayName()
 	{
 		if ( $this->getId() != '' )
 		{
 			if ( $this->get('Release') > 0 )
 			{
 				$caption = translate('Итерация');
 			}
 			elseif( $this->get('Version') > 0 )
 			{
 				$caption = translate('Релиз');
 			}
 			
 			return $caption.' '.$this->get('Caption');
 		}
 		
 		return $this->getId();
 	}
}

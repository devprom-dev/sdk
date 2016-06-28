<?php

class MilestoneIterator extends OrderedIterator
{
 	 function getDisplayName()
 	 {
		$title = getSession()->getLanguage()->getDateFormattedShort($this->get('MilestoneDate'));

		$caption = $this->get('Caption');
		
		if ( $caption != '' ) $title .= ', '.$caption;
		
		return $title;
 	 }

 	 function get( $attr )
 	 {
 	     global $model_factory;
 	     
 	     switch ( $attr )
 	     {
 	         case 'Caption':
 	             
 	            $class = $this->get('ObjectClass');
 	             
 	            $title = parent::get('Caption');
 	             
 	     		if ( $class != '' && $class != 'pm_Milestone' )
        		{
        			$iteration = $model_factory->getObject($class);
        			
        			$iteration_it = $iteration->getExact($this->get('ObjectId'));
        			
        			if ( $title == 'IterationStart' )
        			{
        				$prefix = translate('Начало итерации');
        			}
        
        			if ( $title == 'IterationFinish' )
        			{
        				$prefix = translate('Окончание итерации');
        			}
        			
        			if ( $title == 'ReleaseStart' )
        			{
        				$prefix = translate('Начало релиза');
        			}
        
        			if ( $title == 'ReleaseFinish' )
        			{
        				$prefix = translate('Окончание релиза');
        			}
        
        			return $prefix.' '.$iteration_it->getDisplayName();
        		}
        		
        		return $title;
        		
 	         default:
 	             
 	             return parent::get( $attr );
 	     }
 	 }
 	 
 	 function getViewUrl()
 	 {
 	 	if ( $this->get('ObjectClass')!= '' && $this->get('ObjectClass') != 'pm_Milestone' )
 	 	{
 	 		return '';
 	 	}
 	 	else
 	 	{
 	 		return parent::getViewUrl();
 	 	}
 	 }
}
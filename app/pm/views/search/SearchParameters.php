<?php

class SearchParameters extends InfoSection
{
    function __construct()
    {
        parent::__construct();
        
        $this->setAsyncLoad( false );
    }
    
 	function getCaption() 
 	{
 		return translate('Параметры поиска');
 	}
 	
 	function drawBody()
 	{
 	    global $model_factory;
 	    
 	    echo '<div class="btn-group">';
    		echo '<a class="btn" href="javascript:" onclick="$(\':checkbox\').attr(\'checked\',true);">'.translate('Выбрать все').'</a>';
        echo '</div>';
        
        echo ' &nbsp; ';
        
    	echo '<div class="btn-group">';
    		echo '<a class="btn" href="javascript:" onclick="$(\':checkbox\').attr(\'checked\',false);">'.translate('Очистить').'</a>';
        echo '</div>';
        
 	    echo '<label></label>';
 	    echo '<br/>';
 	    
		$searchable = $model_factory->getObject('SearchableObjectSet');
		
		$searchable_it = $searchable->getAll();
		
		while( !$searchable_it->end() )
		{
			$object = $model_factory->getObject($searchable_it->get('ReferenceName'));

			if ( !getFactory()->getAccessPolicy()->can_read($object) )
			{
				$searchable_it->moveNext();
				
				continue;
			}

			$checked = strtolower($_REQUEST['kind']) == strtolower(get_class($object)) ? 'checked' : 
				( array_key_exists('kind', $_REQUEST ) ? '' : 'checked');
			
			echo '<label class="checkbox">';
          		echo '<input id="'.get_class($object).'" type="checkbox" '.$checked.' > '.$object->getDisplayName();
    		echo '</label>';
			
			$searchable_it->moveNext();
		} 	    
 	}
}
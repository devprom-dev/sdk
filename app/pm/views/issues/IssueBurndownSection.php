<?php

include "ReleaseDatesFrame.php";
include SERVER_ROOT_PATH.'pm/methods/c_stage_methods.php';
include_once SERVER_ROOT_PATH."pm/classes/common/persisters/EntityProjectPersister.php";

class IssueBurndownSection extends InfoSection
{
    var $release_it;
    
    function __construct()
    {
        global $model_factory;
        
 		$release = $model_factory->getObject('Release');
		
		$release->addFilter( new ReleaseTimelinePredicate('current') );
 	 	
		$release->addPersister( new EntityProjectPersister() );
		
		$this->release_it = $release->getAll();
    }
    
 	function getCaption()
 	{
 		return translate('Burndown');
 	}
 	
 	function getIcon()
 	{
 	    return 'icon-fire';
 	}

 	function drawBody()
 	{
 	 	global $model_factory;
 		
 		$project_it = getSession()->getProjectIt();

		$uid = new ObjectUID;
		
		echo '<table>';
		
		while ( !$this->release_it->end() )
		{
		    echo '<tr>';
		    
		    $columns = $this->release_it->count() > 3 ? 3 : 2; 
		    
            while( !$this->release_it->end() && $columns-- > 0 )
            {
                $self_it = $this->release_it->getRef('Project');
                
                echo '<td>';
                
    		    echo '<table class="table"><thead><tr><th>';
    		        echo ($self_it->getId() != $project_it->getId() ? '{'.$self_it->get('CodeName').'} ' : '').
    		            translate('Релиз').': '.$this->release_it->getDisplayName();
    		    echo '</th></tr></thead>';
    		    
    		    echo '<tbody><tr><td>';
    		    
            		$frame = new ReleaseDatesFrame( $this->release_it );
            		
            		$frame->setInfoSection( $this );
            		
            		$frame->draw();
    		    
            	echo '</td></tr></tbody></table>';
    		    
            	echo '</td>';
            	
    		    $this->release_it->moveNext();
            }
            
            echo'</tr>';
		}
		
		echo '</table>';
	}
	
	function IsActive()
	{
		return $this->release_it->count() > 0 && getSession()->getProjectIt()->getMethodologyIt()->HasVelocity(); 
	}
	
	function getActions()
	{
		return array();
		        
		$actions = parent::getActions();
		
		if ( getFactory()->getAccessPolicy()->can_modify($this->release_it) )
		{
			array_push( $actions, array (
				'url' => $this->release_it->getEditUrl(),
				'name' => translate('Изменить')
			));
		}
		
		$method = new ResetBurndownWebMethod();
		if ( $method->hasAccess() )
		{
			array_push( $actions, array() );
			array_push( $actions, array( 
				'url' => $method->url( $this->release_it ),
				'name' => $method->getCaption() 
			));
		}
		
		return $actions;
	}
}  
<?php

class PMWikiList extends PMPageList
{
 	var $version_it, $stage, $form, $form_render_parms;
 	
	function retrieve()
	{
		global $model_factory, $session;
		
		parent::retrieve();

		$object = $this->getObject();
		
		$this->version_it = $object->getVersionsIt();
		
		$this->version_it->buildPositionHash( array('WikiPageId') ); 

		$this->stage = $model_factory->getObject('Stage');
		
		$this->stage->disableVpd();
	}
	
	function & getStateObject()
	{
	    return $this->getTable()->getStateObject();
	}
	
	function Statable( $object = null )
	{
		if ( is_object($object) )
		{
			return $object->IsStatable();
		}
		
		return false;
	}

  	function IsNeedToSelect()
	{
		return true;
	}
	
	function drawCell( $object_it, $attr ) 
	{
		switch ( $attr )
		{
			case 'Caption':
				if ( $object_it->get('BrokenTraces') != "" )
				{
					echo $this->getTable()->getView()->render('pm/WikiPageBrokenIcon.php', 
							array ( 
									'id' => $object_it->getId(),
									'url' => getSession()->getApplicationUrl($object_it) 
							)
					);					
				}
				
				echo $object_it->getDisplayName();

				break;
				
			case 'Stage':
				$stages = array();
				
				$this->version_it->setStop( 'WikiPageId', $object_it->getId() );
				$releases = array();
				
				while ( !$this->version_it->end() )
				{
					if ( in_array( $this->version_it->get('Version'), $releases) )
					{
						$this->version_it->moveNext();
						continue;
					}
					
					if ( $this->version_it->get('Release') > 0 )
					{
    					$stage_it = $this->stage->getExact( 
    						$this->version_it->get('Version').'.'.$this->version_it->get('Release') );
					}
					else if ( $this->version_it->get('Version') > 0 )
					{
    					$stage_it = $this->stage->getExact( $this->version_it->get('Version') );
					}
					
					if( is_object($stage_it) ) 
					{
						array_push( $releases, $this->version_it->get('Version') );
						array_push( $stages, $stage_it->getDisplayName() );
					}

					$this->version_it->moveNext();
				}

				echo join(array_unique($stages), ', ');
				break;
				
			default:
				
				parent::drawCell( $object_it, $attr );
		}
	}

	function drawRefCell( $entity_it, $object_it, $attr ) 
	{
		switch ( $entity_it->object->getClassName() )
		{
			case 'WikiPage':
				
				$uid = new ObjectUid;

				$baselines = array();
						
				foreach( preg_split('/,/', $object_it->get('SourcePageBaseline')) as $trace )
				{
					list( $page_id, $baseline ) = preg_split('/:/', $trace);
					
					$baselines[$page_id] = $baseline;
				}

				$items = array();
				
				while( !$entity_it->end() )
				{
              		$uid->setBaseline( $baselines[$entity_it->getId()] );
                			
           			$items[] = $uid->getUidIconGlobal($entity_it, true);
					
					$entity_it->moveNext();
				}
				
				echo '<div class="tracing-ref">';
					if ( $entity_it->get('BrokenTraces') != "" )
					{
						echo $this->getTable()->getView()->render('pm/WikiPageBrokenIcon.php', 
								array ( 
										'id' => $entity_it->getId(),
										'url' => getSession()->getApplicationUrl($entity_it)
								)
						);
					}
					echo join($items, ', ');
				echo '</div>';
				
				break;
				
		    case 'WikiPageFile':
		        
		        $files = array();
		        
		        while( !$entity_it->end() )
		        {
		            $files[] = array (
		                    'type' => $entity_it->IsImage('Content') ? 'image' : 'file',
		                    'url' => $entity_it->getFileUrl(),
		                    'name' => $entity_it->getFileName('Content'),
		                    'size' => $entity_it->getFileSizeKb('Content')
		            );  
		            
		            $entity_it->moveNext();
		        }

		        echo $this->getTable()->getView()->render('core/Attachments.php', array( 'files' => $files ));
		        
		        break;
				
			default:
				parent::drawRefCell( $entity_it, $object_it, $attr );
		}
	}
	
	function getColumnFields()
	{
		$fields = parent::getColumnFields();
		
		foreach( $fields as $key => $value )
		{
			if ( $value == 'Content' ) unset($fields[$key]);
		}
		
		return $fields;
	}
	
	function getGroupFields()
	{
		$fields = array();
		
		$object = $this->getObject();
		
		if ( is_object($object->getTypeIt()) )
		{
			array_push( $fields, 'PageType' );
		}
		
		if ( $object->IsStatable() )
		{
			array_push( $fields, 'State' );
		}
		
		array_push( $fields, 'Project', 'ChangeRequest', 'Tags', 'DocumentId' );
		
		if ( getSession()->getProjectIt()->getMethodologyIt()->HasFeatures() )
		{
			array_push( $fields, 'Feature' );
		}
		
		return $fields;
	}
	
	function IsNeedToSelectRow( $object_it )
	{
        return true;
	}
	
 	function getColumnWidth( $column )
 	{
 	    switch ( $column )
 	    {
 	        case 'State':
 	            return '1%';
 	            
 	        case 'Progress':
 	        case 'DocumentVersion':
 	        case 'Stage':
 	            return '5%';
 	        
 	        default:
 	            return parent::getColumnWidth( $column );
 	    }
 	}
}
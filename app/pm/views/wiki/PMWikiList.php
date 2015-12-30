<?php

class PMWikiList extends PMPageList
{
 	var $version_it, $stage, $form, $form_render_parms;
 	
	function retrieve()
	{
		parent::retrieve();

		$this->stage = getFactory()->getObject('Stage');
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
				if ( $object_it->get('BrokenTraces') != "" ) {
					echo $this->getTable()->getView()->render('pm/WikiPageBrokenIcon.php', 
						array (
							'id' => $object_it->getId(),
							'url' => getSession()->getApplicationUrl($object_it)
						)
					);					
				}
				echo $object_it->getDisplayName();
				break;
				
			case 'Workflow':
                if ( $object_it->get($attr) != '' ) {
                    $lines = array();
                    $rows = json_decode($object_it->getHtmlDecoded($attr), true);
                    foreach( $rows as $row ) {
                        $lines[] = '<div style="white-space:nowrap">'.str_replace('%1', $row['action'],
                                        str_replace('%2', $row['author'],
                                                str_replace('%3', getSession()->getLanguage()->getDateTimeFormatted($row['date']), text(2045)))).'</div>';
                    }
                    echo join('', $lines);
                }
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
				echo '<div class="tracing-ref">';
					if ( $entity_it->get('BrokenTraces') != "" ) {
						echo $this->getTable()->getView()->render('pm/WikiPageBrokenIcon.php', 
							array (
								'id' => $entity_it->getId(),
								'url' => getSession()->getApplicationUrl($entity_it)
							)
						);
					}
					parent::drawRefCell( $entity_it, $object_it, $attr );
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
		
		foreach( $fields as $key => $value ) {
			if ( $value == 'Content' ) unset($fields[$key]);
		}
		
		$fields[] = 'SectionNumber';
		
		return $fields;
	}
	
	function getGroupFields()
	{
		$fields = array_diff(
			parent::getGroupFields(),
			$this->getObject()->getAttributesByGroup('source-attribute'),
		    array (
				'Watchers', 'Attachments', 'ParentPage'
			)
		);
		
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
 	        case 'SectionNumber':
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
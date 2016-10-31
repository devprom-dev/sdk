<?php
include_once SERVER_ROOT_PATH . "pm/classes/wiki/persisters/WikiPageDocumentGroupPersister.php";

class PMWikiList extends PMPageList
{
 	var $version_it, $form, $form_render_parms;
	private $displayContent = false;
	private $searchText = '';
 	
	function retrieve()
	{
	    $this->getObject()->addPersister( new WikiPageDocumentGroupPersister() );
		if ( $this->displayContent ) {
			$this->getObject()->setRegistry( new WikiPageRegistryContent($this->getObject()) );
		}
		parent::retrieve();
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
                if ( $this->displayContent ) {
                    $title = $object_it->getHtmlDecoded('CaptionLong');
                }
                else {
                    $title = $object_it->getDisplayName();
                }
				if ( $object_it->get('BrokenTraces') != "" ) {
					$title = $this->getTable()->getView()->render('pm/WikiPageBrokenIcon.php',
						array (
							'id' => $object_it->getId(),
							'url' => getSession()->getApplicationUrl($object_it)
						)
					).$title;
				}
				if ( $this->displayContent ) {
					echo '<h5 class="bs">'.$title.'</h5>';
				}
				else {
					echo $title;
				}
				if ( $this->displayContent && trim($object_it->get('Content')," \r\n") != '' ) {
					$field = new FieldWYSIWYG($object_it->get('ContentEditor'));
					$field->setValue($object_it->get('Content'));
					$field->setObjectIt($object_it);
					$field->setSearchText($this->searchText);
                    echo '<div class="reset wysiwyg">';
					    echo $field->getText(true);
                    echo '</div>';
				}
				break;
				
			case 'Workflow':
                if ( $object_it->get($attr) != '' ) {
                    $lines = array();
                    $rows = json_decode($object_it->getHtmlDecoded($attr), true);
                    foreach( $rows as $row ) {
						$line = $this->getTable()->getView()->render('core/UserPicture.php', array (
							'id' => $row['author_id'],
							'class' => 'user-mini',
							'image' => 'userpics-mini',
							'title' => $row['author']
						));
						$line .= " " . $row['action'];
						$line .= ", " . getSession()->getLanguage()->getDateFormattedShort($row['date']);
                        $lines[] = '<div class="workflow-history">'.$line.'</div>';
                    }
                    echo join('', $lines);
                }
				break;

			case 'Dependency':
				$uids = array();
				foreach( preg_split('/,/', $object_it->get($attr)) as $object_info )
				{
					list($class, $id) = preg_split('/:/',$object_info);
					if ( !class_exists($class,false) ) continue;
					$ref_it = getFactory()->getObject($class)->getExact($id);
					$uids[] = $this->getUidService()->getUidIcon($ref_it);
				}
				echo join(' ',$uids);
				break;

			default:
				parent::drawCell( $object_it, $attr );
		}
	}

	function drawRefCell( $entity_it, $object_it, $attr ) 
	{
		switch ( $entity_it->object->getClassName() )
		{
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

		        echo $this->getTable()->getView()->render('core/Attachments.php', array(
		            'files' => $files,
                    'random' => $entity_it->getId()
                ));
		        
		        break;
				
			default:
				parent::drawRefCell( $entity_it, $object_it, $attr );
		}
	}
	
	function getColumnFields()
	{
		$fields = parent::getColumnFields();
		$fields[] = 'SectionNumber';
		return $fields;
	}
	
	function getGroupFields()
	{
		$fields = array_diff(
			parent::getGroupFields(),
			$this->getObject()->getAttributesByGroup('source-attribute'),
		    array (
				'Watchers', 'Attachments'
			)
		);

		return $fields;
	}
	
	function IsNeedToSelectRow( $object_it ) {
        return true;
	}

	function getColumnVisibility($attr) {
		return $attr == 'Content' ? false : parent::getColumnVisibility($attr);
	}

	function getColumnName($attr)
	{
		switch( $attr ) {
			case 'Caption':
				if ( $this->displayContent ) return text('2121');
				return parent::getColumnName($attr);
		}
		return parent::getColumnName($attr);
	}

	function getRowBackgroundColor( $object_it )
	{
		if ( $this->displayContent ) return '';
		return parent::getRowBackgroundColor( $object_it );
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

	function getRenderParms()
	{
        $parms = parent::getRenderParms();

		$values = $this->getFilterValues();

		if ( in_array($values['search'], array('','all','none')) ) {
			$values['search'] = '';
		}
		$this->searchText = $values['search'];
		$this->displayContent = !in_array($this->searchText, array('','all','hide')) || parent::getColumnVisibility('Content');

		return array_merge( $parms,
			array (
				'table_class_name' => $this->displayContent ? 'table wishes-table' : $parms['table_class_name']
			)
		);
	}
}
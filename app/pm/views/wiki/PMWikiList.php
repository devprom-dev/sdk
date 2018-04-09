<?php
include_once SERVER_ROOT_PATH . "pm/classes/wiki/persisters/WikiPageDocumentGroupPersister.php";
include_once SERVER_ROOT_PATH . "pm/classes/wiki/persisters/WikiPageUsedByPersister.php";
include_once SERVER_ROOT_PATH . "pm/views/ui/WorkflowProgressFrame.php";
include "fields/FieldWikiEstimation.php";

class PMWikiList extends PMPageList
{
 	var $version_it, $form, $form_render_parms;
	private $displayContent = false;
	private $searchText = '';
    private $workflowFrame = null;

	function retrieve()
	{
	    $this->getObject()->addPersister( new WikiPageDocumentGroupPersister() );
		if ( $this->displayContent ) {
			$this->getObject()->setRegistry( new WikiPageRegistryContent($this->getObject()) );
		}
		parent::retrieve();
	}

    protected function getPersisters( $object, $sorts )
    {
        $persisters = array();
        if ( $this->IsAttributeInQuery('UsedBy') ) {
            $persisters[] = new WikiPageUsedByPersister();
        }
        return array_merge(
            parent::getPersisters($object, $sorts),
            $persisters
        );
    }

	function & getStateObject()
	{
	    return $this->getTable()->getStateObject();
	}
	
	function Statable( $object = null )
	{
		if ( is_object($object) ) return $object->IsStatable();
		return false;
	}

  	function IsNeedToSelect()
	{
		return true;
	}

	function getTitle( $object_it ) {
	    return $object_it->getDisplayName();
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
                    $title = $this->getTitle($object_it);
                }
				if ( $object_it->get('BrokenTraces') != "" ) {
					$title = $this->getTable()->getView()->render('pm/WikiPageBrokenIcon.php',
						array (
							'id' => $object_it->getId(),
							'url' => getSession()->getApplicationUrl($object_it)
						)
					).$title;
				}
				if ( $this->displayContent && $object_it->get('Content') != '' ) {
					echo '<h5 class="bs">'.$title.'</h5>';
				}
				else {
					echo $title;
				}
                if ( $this->checkColumnHidden('Tags') && $object_it->get('Tags') != '' ) {
                    echo ' ';
                    $this->drawRefCell($this->getFilteredReferenceIt('Tags', $object_it->get('Tags')), $object_it, 'Tags');
                }
				if ( $this->displayContent ) {
                    $filterValues = $this->getFilterValues();
                    if ( $filterValues['compareto'] != '' ) {
                        $compareIt = $this->getObject()->getRegistry()->Query(
                            array(
                                new FilterAttributePredicate('UID', $object_it->get('UID')),
                                new WikiPageBranchFilter($filterValues['compareto'])
                            )
                        );
                        echo '<div class="reset wysiwyg">';
                        $field = new FieldCompareToContent($object_it->copy(),$compareIt);
                        $field->draw();
                        echo '</div>';
                    }
                    else if ( trim($object_it->get('Content')," \r\n") != '' ) {
                        $field = new FieldWYSIWYG($object_it->get('ContentEditor'));
                        $field->setValue($object_it->get('Content'));
                        $field->setObjectIt($object_it);
                        $field->setSearchText($this->searchText);
                        $field->drawReadonly();
                    }
				}
				break;

            case 'Estimation':
                if ( $object_it->get('TotalCount') > 0 ) {
                    echo getSession()->getLanguage()->getHoursWording($object_it->get('EstimationCumulative'));
                }
                else {
                    if ( is_object($this->estimation_field) && $object_it->get('TotalCount') < 1 ) {
                        $this->estimation_field->setObjectIt($object_it);
                        $this->estimation_field->draw($this->getTable()->getView());
                    }
                    else {
                        parent::drawCell($object_it, $attr);
                    }
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

            case 'State':
                echo '<table class="state-rich"><tr>';
                echo '<td style="vertical-align: top;">';
                    parent::drawCell($object_it, $attr);
                echo '</td>';
                    if ( $object_it->get('ParentPage') == '' && $object_it->get('TotalCount') > 0 ) {
                        echo '<td>';
                        $object = getFactory()->getObject(get_class($this->getObject()));
                        $object->addFilter( new WikiDocumentWaitFilter($object_it->getId()) );
                        $aggregateFunc = new AggregateBase( 'State', 'WikiPageId', 'COUNT' );
                        $object->addAggregate($aggregateFunc);
                        $agg_it = $object->getAggregated('t', array(new SortAttributeClause('State')));
                        if ( $agg_it->count() > 1 ) {
                            $this->workflowFrame->draw(
                                $agg_it,
                                $aggregateFunc,
                                '&type=all&document='.$object_it->getId()
                            );
                        }
                        echo '</td>';
                    }
                echo '</tr></table>';
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
		return array_merge(
		    $fields,
            array (
                'RecordModified'
            )
        );
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
        $this->workflowFrame = new WorkflowProgressFrame(
            $this->getObject(), str_replace('/docs', '/list', $this->getObject()->getPage())
        );

        $parms = parent::getRenderParms();

		$values = $this->getFilterValues();

		if ( in_array($values['search'], array('','all','none')) ) {
			$values['search'] = '';
		}
		$this->searchText = $values['search'];
		$this->displayContent = !in_array($this->searchText, array('','all','hide')) || parent::getColumnVisibility('Content');

        if ( parent::getColumnVisibility('Estimation') ) {
            $this->estimation_field = new FieldWikiEstimation($this->getObject());
        }

		return array_merge( $parms,
			array (
				'table_class_name' => $this->displayContent ? 'table wishes-table' : $parms['table_class_name']
			)
		);
	}
}
<?php
use Devprom\ProjectBundle\Service\Widget\WidgetService;
include_once SERVER_ROOT_PATH . "pm/classes/wiki/persisters/WikiPageDocumentGroupPersister.php";
include_once SERVER_ROOT_PATH . "pm/classes/wiki/persisters/WikiPageUsedByPersister.php";
include_once SERVER_ROOT_PATH . "pm/views/ui/WorkflowProgressFrame.php";
include "fields/FieldWikiEstimation.php";

class PMWikiList extends PMPageList
{
	private $displayContent = false;
	private $inlineSectionNumber = false;
	private $searchText = '';
    private $workflowFrame = null;
    private $typeField = null;
    private $baselineIt = null;

    function extendModel()
    {
        if ( $this->getObject()->getStateClassName() != '' ) {
            $this->getObject()->addAttribute('Readiness', '', translate('Готовность'), false, false, '',
                $this->getObject()->getAttributeOrderNum('State') + 1);
        }

        parent::extendModel();
    }

    function retrieve()
	{
        $this->workflowFrame = new WorkflowProgressFrame(
            $this->getObject(), str_replace('/docs', '/list', $this->getObject()->getPage())
        );

	    $this->getObject()->addPersister( new WikiPageDocumentGroupPersister() );
		if ( $this->displayContent ) {
			$this->getObject()->setRegistry( new WikiPageRegistryContent($this->getObject()) );
		}

        if ( getFactory()->getAccessPolicy()->can_modify_attribute($this->getObject(), 'PageType') ) {
            $this->typeField = new FieldReferenceAttribute(
                $this->getObject()->getEmptyIterator(),
                'PageType',
                $this->getObject()->getAttributeObject('PageType')
            );
        }

        $filterValues = $this->getFilterValues();
        $baseline = $this->getTable()->getBaselineObject();
        $baselines = \TextUtils::parseFilterItems($filterValues['branch']);
        if ( count($baselines) > 0 ) {
            $this->baselineIt = $baseline->getExact($baselines);
        }
        else {
            $this->baselineIt = $baseline->getEmptyIterator();
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
                    $title = $object_it->get('CaptionLong');
                }
                else {
                    $title = $this->getTitle($object_it);
                }
				if ( $object_it->get('Suspected') > 0 ) {
                    $title = WidgetService::getHtmlBrokenIcon($object_it->getId(), getSession()->getApplicationUrl($object_it)) . $title;
				}

				if ( $this->inlineSectionNumber ) {
                    $title = $object_it->get('SectionNumber') . ' &nbsp; ' . $title;
                }

				if ( strpos($title, $object_it->getHtmlDecoded('DocumentVersion')) === false && $object_it->get('ParentPage') == '' && $object_it->get('DocumentVersion') != '' ) {
				    echo '[' . $object_it->get('DocumentVersion') . '] ';
                }

    			echo $title;

                if ( $this->checkColumnHidden('Tags') && $object_it->get('Tags') != '' ) {
                    echo ' ';
                    $this->drawRefCell($this->getFilteredReferenceIt('Tags', $object_it->get('Tags')), $object_it, 'Tags');
                }
				if ( $this->displayContent ) {
                    $filterValues = $this->getFilterValues();

                    $content = $object_it->get('Content');
                    if ( $filterValues['branch'] != '' ) {
                        $snapshot = new WikiPageComparableSnapshot(
                            $object_it->get('DocumentId') == '' ? $object_it : $object_it->getRef('DocumentId')
                        );
                        $snapshotIt = $snapshot->getAll();
                        $snapshotIt->moveToId($filterValues['branch']);
                        if ( $snapshotIt->getId() != '' ) {
                            $registry = new WikiPageRegistryComparison($this->getObject());
                            $registry->setPageIt($object_it);
                            $registry->setBaselineIt($snapshotIt);
                            $content = $registry->Query()->get('Content');
                        }
                    }

                    if ( $filterValues['compareto'] != '' ) {
                        $snapshot = new WikiPageComparableSnapshot(
                            $object_it->get('DocumentId') == '' ? $object_it : $object_it->getRef('DocumentId')
                        );
                        $snapshotIt = $snapshot->getAll();
                        $snapshotIt->moveTo('Caption', $filterValues['compareto']);
                        if ( $snapshotIt->getId() != '' ) {
                            $registry = new WikiPageRegistryComparison($this->getObject());
                            $registry->setPageIt($object_it);
                            $registry->setBaselineIt($snapshotIt);
                            $compareToPageIt = $registry->Query();
                            if ( $compareToPageIt->getId() != '' ) {
                                echo '<div class="reset wysiwyg">';
                                    $field = new FieldCompareToContent($object_it,
                                        html_entity_decode($content), $compareToPageIt->getHtmlDecoded('Content'));
                                    $field->draw();
                                echo '</div>';
                                break;
                            }
                        }
                    }
                    if ( trim($object_it->get('Content')," \r\n") != '' ) {
                        $field = new FieldWYSIWYG($object_it->get('ContentEditor'));
                        $field->setValue($content);
                        $field->setObjectIt($object_it);
                        $field->setSearchText($this->searchText);
                        $field->drawReadonly();
                    }
				}
				break;

            case 'Estimation':
                if ( is_object($this->estimation_field) && $object_it->get('TotalCount') < 1 && $object_it->getId() != '' ) {
                    $this->estimation_field->setObjectIt($object_it);
                    $this->estimation_field->draw($this->getRenderView());
                }
                else {
                    parent::drawCell($object_it, $attr);
                }
                break;
				
			case 'Workflow':
                if ( $object_it->get($attr) != '' ) {
                    $lines = array();
                    $rows = json_decode($object_it->getHtmlDecoded($attr), true);
                    foreach( $rows as $row ) {
						$line = $this->getRenderView()->render('core/UserPicture.php', array (
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

            case 'Readiness':
                echo '<div class="state-rich">';
                    $object = getFactory()->getObject(get_class($this->getObject()));
                    $object->addFilter( new WikiDocumentFilter($object_it) );
                    $aggregateFunc = new AggregateBase( 'State', 'WikiPageId', 'COUNT' );
                    $object->addAggregate($aggregateFunc);
                    $agg_it = $object->getAggregated('t', array(new SortAttributeClause('State')));
                    $this->workflowFrame->draw(
                        $agg_it,
                        $aggregateFunc,
                        '&type=all&document='.$object_it->getId()
                    );
                echo '</div>';
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

		        echo $this->getRenderView()->render('core/Attachments.php', array(
		            'files' => $files,
                    'random' => $entity_it->getId()
                ));
		        break;

			default:
			    switch ( $attr ) {
                    case 'PageType':
                        if ( is_object($this->typeField) ) {
                            $this->typeField->setObjectIt($object_it);
                            $this->typeField->draw($this->getRenderView());
                        }
                        else {
                            parent::drawRefCell( $entity_it, $object_it, $attr );
                        }
                        break;

                    default:
                        parent::drawRefCell( $entity_it, $object_it, $attr );
                }
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
		    array (
				'Watchers', 'Attachments'
			)
		);
		return array_merge(
		    $fields,
            array (
                'RecordModified',
                'DocumentVersion'
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
 	        case 'Stage':
 	            return '5%';

            case 'Readiness':
                return '110';
 	        
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
		$this->displayContent = count(\TextUtils::parseFilterItems($this->searchText)) > 0
            || parent::getColumnVisibility('Content') || count(\TextUtils::parseFilterItems($values['compareto'])) > 0;

        if ( parent::getColumnVisibility('Estimation') ) {
            $this->estimation_field = new FieldWikiEstimation($this->getObject());
        }

		return array_merge( $parms,
			array (
				'table_class_name' => $this->displayContent ? 'table wishes-table' : $parms['table_class_name']
			)
		);
	}

	function getObjectBaselineIt( $objectIt ) {
        $this->baselineIt->moveTo('ObjectId', $objectIt->getId());
        return $this->baselineIt->copy();
    }
}
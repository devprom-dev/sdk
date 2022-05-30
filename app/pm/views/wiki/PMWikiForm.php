<?php
use Devprom\ProjectBundle\Service\Wiki\WikiBaselineService;
include_once SERVER_ROOT_PATH.'pm/views/wiki/editors/WikiEditorBuilder.php';
include_once SERVER_ROOT_PATH.'pm/views/watchers/FieldWatchers.php';
include_once SERVER_ROOT_PATH."pm/views/ui/FieldHierarchySelector.php";
include_once SERVER_ROOT_PATH.'pm/methods/OpenBrokenTraceWebMethod.php';
include_once SERVER_ROOT_PATH.'pm/methods/ReintegrateWikiTraceWebMethod.php';
include_once SERVER_ROOT_PATH."ext/locale/LinguaStemRu.php";
include_once SERVER_ROOT_PATH.'pm/classes/wiki/converters/WikiConverter.php';
include_once "fields/FieldWikiAttachments.php";
include_once "fields/FieldWikiTagTrace.php";
include_once "fields/FieldWikiTrace.php";
include_once "fields/FieldCompareToContent.php";
include_once "fields/FieldCompareToCaption.php";
include_once "fields/FieldWikiPageDependency.php";

class PMWikiForm extends PMPageForm
{
	var $review_mode = false;
	var $readonly_mode = false;
	var $form_index = '';

	private $revision_it;
	private $page_to_compare_it;
	private $document_it;
	private $descriminator_value = null;
	private $editable = true;
	private $appendable = true;
	private $append_methods = array();
	private $delete_method = null;
	private $search_text = array();
    private $exportMethods = array();
    private $create_task_actions = array();
    private $attributesVisibility = array();
    private $sectionIt = null;

	function __construct($object)
	{
		$object->addPersister(new WatchersPersister());

		parent::__construct($object);

		$this->editable = getFactory()->getAccessPolicy()->can_modify($object);
		$this->appendable = getFactory()->getAccessPolicy()->can_create($object);
		$this->sectionIt = $object->getEmptyIterator();

        $this->buildMethods();
	}

	function getIterator($objectId)
    {
        if ( $this->getEditMode() ) {
            $this->getObject()->addPersister(new WikiPageUsedByPersister());
        }
        return parent::getIterator($objectId);
    }

    function setObjectIt($object_it)
    {
        $this->sectionIt = is_object($object_it)
            ? $object_it
            : $this->getObject()->getEmptyIterator();

        if ( is_object($object_it) && $object_it->get('Includes') != '' && !$this->getEditMode() ) {
            parent::setObjectIt($object_it->object->getExact($object_it->get('Includes')));
        }
        else {
            parent::setObjectIt($object_it);
        }
    }

    function getSectionIt() {
	    return $this->sectionIt;
    }

    protected function buildMethods()
	{
	    $methodologyIt = getSession()->getProjectIt()->getMethodologyIt();

		$method = new DocNewChildWebMethod($this->getObject());
		if ( $method->hasAccess() ) {
            $type_it = $this->getObject()->getTypeIt();
            if ( is_object($type_it) ) {
                while( !$type_it->end() ) {
                    $this->append_methods[] = array(
                        'name' => $type_it->getDisplayName(),
                        'referenceName' => $type_it->get('ReferenceName'),
                        'method' => $method,
                        'parms' => array('PageType' => $type_it->getId())
                    );
                    $type_it->moveNext();
                }
            }
            if ( count($this->append_methods) < 1 ) {
                $this->append_methods[] = array(
                    'name' => $this->getAppendActionName(),
                    'method' => $method,
                    'parms' => array()
                );
            }
		}

		$method = new BulkDeleteWebMethod();
		if ($this->checkAccess() && !$this->getReadonly() && $method->hasAccess()) {
			if ($this->IsFormDisplayed() || $this->getReviewMode()) {
				$method->setRedirectUrl('function() { if ( typeof loadContentTree != \'undefined\' ) loadContentTree();}');
			}
			$this->delete_method = $method;
		}

        $method = new ObjectCreateNewWebMethod(getFactory()->getObject('Task'));
        if ( $method->hasAccess() && $methodologyIt->HasTasks() ) {
            $task_types = $this->getTaskTypesRelated();
            if ( count($task_types) > 0 ) {
                $type_it = getFactory()->getObject('TaskType')->getByRefArray(
                    array (
                        'ReferenceName' => $task_types
                    )
                );
            }
            $this->create_task_actions[] = array(
                'name' => $method->getCaption(),
                'method' => $method,
                'type' => count($task_types) > 0 ? $type_it->getId() : 0
            );
        }

        $method = $this->buildExportWebMethod();
        $methodPageIt = $this->getObject()->createCachedIterator(
            array (
                array (
                    'WikiPageId' => '%ids%'
                )
            )
        );
		$converter = new WikiConverter( $this->getObject() );
        $converter_it = $converter->getAll();
        while( !$converter_it->end() ) {
            $this->exportMethods[] = array(
                'name' => $converter_it->get('Caption'),
                'url' => $method->url(
                            $methodPageIt,
                            $converter_it->get('EngineClassName'),
                            $converter_it->get('Caption'),
                            '%baseline%',
                            '%compare%'
                        )
            );
            $converter_it->moveNext();
        }
	}

	protected function extendModel()
	{
		$object = $this->getObject();

		foreach (array('OrderNum', 'ContentEditor') as $attribute) {
			$object->setAttributeVisible($attribute, false);
		}
		$object->setAttributeVisible('ParentPage', $this->getEditMode());

        foreach (array('Caption', 'Content', 'Attachments', 'Tags', 'Watchers') as $attribute) {
            $object->setAttributeVisible($attribute, true);
        }

		$object_it = $this->getObjectIt();
		if ( is_object($object_it) ) {
			if ( $object_it->get('Dependency') != '' ) {
				$object->setAttributeVisible('Dependency', true);
			}
			if ( $object_it->get('Feature') != '' ) {
				$object->setAttributeVisible('Feature', true);
			}
        }
		else {
		    if ( $_REQUEST['ParentPage'] != '' ) {
                $object->setAttributeEditable('DocumentVersion', false);
            }
        }

		parent::extendModel();
	}

	function setSearchText($text) {
		$this->search_text = $text;
	}

	protected function buildExportWebMethod() {
	    return new WikiExportBaseWebMethod();
    }

	function getDiscriminator()
	{
		if (!is_null($this->descriminator_value)) return $this->descriminator_value;

		$value = $this->getFieldValue($this->getDiscriminatorField());

		if ($value == '') return $this->descriminator_value = '';

		return $this->descriminator_value =
				$this->getObject()->getAttributeObject($this->getDiscriminatorField())
						->getExact($value)->get('ReferenceName');
	}

	function getRedirectUrl()
	{
		if ($this->getAction() == 'delete') {
			$object_it = $this->getObjectIt();

			$root_it = $object_it->getRootIt();

			if ($root_it->getId() > 0) {
				$uid = new ObjectUID();

				$info = $uid->getUidInfo($root_it);

				return $info['url'];
			} else {
				return $this->getObject()->getPage();
			}
		}

		$object_it = $this->getObjectIt();

		if (is_object($object_it) && $object_it->get('IsDocument') == 1) {
            $uid = new ObjectUID();
            $info = $uid->getUidInfo($object_it);
            return $info['url'];
		}

		return '';
	}

	function processEmbeddedForms($object_it, $callback = null )
	{
		if (!is_object($object_it)) {
			throw new Exception('Trying to process empty iterator object');
		}
		if ($object_it->getId() == '') {
			throw new Exception('Trying to process empty object');
		}
		parent::processEmbeddedForms($object_it, $callback);
	}

	function persist()
    {
        if (!parent::persist()) return false;

        if ( $this->getAction() == 'add' ) {
            $objectIt = $this->getObjectIt();
            if ( $objectIt->get('ParentPage') == '' && $objectIt->get('DocumentVersion') != '' )
            {
                $service = new WikiBaselineService(getFactory(), getSession());
                $service->storeInitialBaseline($objectIt);
                $it = getFactory()->getObject('Baseline')->getAll();
                $baselineIt = $it->object->getEmptyIterator();
                while( !$it->end() ) {
                    if ( $it->getId() == $objectIt->get('DocumentVersion') ) {
                        $baselineIt = $it->copy();
                        break;
                    }
                    $it->moveNext();
                }
                $service->storeBranch($objectIt, $baselineIt, $objectIt->get('DocumentVersion'));
            }
        }

        return true;
    }

    function getTraceObject()
	{
		return getFactory()->getObject('WikiPageTrace');
	}

	function getAppendMethods()
	{
		return $this->append_methods;
	}

	function setFormIndex($index)
	{
		$this->form_index = $index;
	}

	function getAppendActionName()
	{
		return $this->getObject()->getDisplayName();
	}

	function setReviewMode()
	{
		$this->review_mode = true;
	}

	function getReviewMode()
	{
		return $this->review_mode;
	}

	function setReadonly($readonly = true)
	{
		$this->readonly_mode = $readonly;
	}

	function getReadonly()
	{
		return $this->readonly_mode;
	}

	function setRevisionIt($revision_it)
	{
		$this->revision_it = $revision_it;
	}

	function getRevisionIt()
	{
		return $this->revision_it;
	}

	function setCompareTo($page_it)
	{
		$this->page_to_compare_it = $page_it;
	}

	function getCompareTo()
	{
		return $this->page_to_compare_it;
	}

	function setDocumentIt($document_it)
	{
		$this->document_it = $document_it;
	}

	function getDocumentIt()
	{
		return $this->document_it;
	}

	function IsWatchable($page_it)
	{
		return true;
	}

	function getNewRelatedActions()
	{
		$actions = array();
		$page_it = $this->getObjectIt();

        if ( count($this->create_task_actions) > 0 && is_object($page_it) && !$this->getObject() instanceof ProjectPage) {
            $task_parms = array (
                'Caption' => $page_it->getHtmlDecoded('CaptionLong'),
                get_class($this->getObject()) => $page_it->getId(),
                'DocumentBaseline' => $page_it->get('CurrentBaseline')
            );
            $task_actions = array();
            foreach( $this->create_task_actions as $action ) {
                $method = $action['method'];
                $task_actions[] = array(
                    'name' => $action['name'],
                    'url' => $method->getJSCall( array_merge($task_parms, array(
                        'TaskType' => $action['type']
                    ))),
                    'uid' => 'implement-task-'.$action['type']
                );
            }
            $actions = array_merge($actions, array(array()), $task_actions, array(array()));
        }

        if ( is_object($this->getCompareTo())) return $actions;

        $not_readonly = !$this->getReadonly() && !$this->getEditMode();
		if ($this->appendable && $not_readonly) {
            $appendActions = array();
			foreach ($this->append_methods as $action) {
				$method = $action['method'];
				if ( is_object($page_it) ) {
					$method->setVpd($page_it->get('VPD'));
				}
				$parms = array_merge($action['parms'],
						array(
                            'ParentPage' => is_object($page_it) ? $page_it->getId() : ''
						));
				$action['url'] = $method->getJSCall($parms);
				$action['uid'] = 'append-child-page'.($action['referenceName'] != '' ? '-'.$action['referenceName'] : '');
                $appendActions[] = $action;
			}

			$actions[] = array();
            $actions['children'] = array(
                'name' => translate('Дочернее'),
                'items' => $appendActions
            );
		}

		return $actions;
	}

	function getDeleteActions( $objectIt )
	{
		$actions = array();
        $pageIt = $this->getSectionIt();

		if (is_object($this->delete_method) && is_object($pageIt)) {
			$actions[] = array(
                'name' => $pageIt->getId() != $objectIt->getId()
                            ? translate('Исключить')
                            : $this->delete_method->getCaption(),
                'url' => $this->delete_method->url(
                            $pageIt->object,
                            $pageIt->getId()
                        )
			);
		}

		return $actions;
	}

	function getExportActions( $object_it )
	{
		$actions = array();

        foreach( $this->exportMethods as $action ) {
            $parms = \TextUtils::buildIds($object_it->idsToArray());
            if ( $parms != '' ) {
                $action['url'] = preg_replace('/%(id|ids)%/', $parms, $action['url']);
            }
            $action['url'] = preg_replace('/%baseline%/',
                is_object($this->getRevisionIt() && $this->getRevisionIt()->getId() > 0) ? $this->getRevisionIt()->getId() : '0', $action['url']);

            $compareToIt = $this->getCompareTo();
            $compareToId = '';
            if ( is_object($compareToIt) && $compareToIt->getId() != '' ) {
                if ( $compareToIt->object instanceof WikiPageComparableSnapshot ) {
                    $compareToId = $compareToIt->getId();
                }
                else {
                    $compareToId = 'document'.$compareToIt->getId();
                }
            }
            $action['url'] = preg_replace('/%compare%/',
                $compareToId != '' ? $compareToId : '0', $action['url']);

            $actions[] = $action;
        }

        return $actions;
	}

    function getModifyActions( $objectIt ) {
	    if ( $objectIt->getId() != $this->getSectionIt()->getId() ) {
            return array(
                'modify' => array(
                    'name' => translate('Открыть'),
                    'url' => $objectIt->getUidUrl()
                )
            );
        }
        return parent::getModifyActions($objectIt);
    }

	function getActions($page_it = null)
	{
		if (!is_object($page_it)) $page_it = $this->getObjectIt();
		if (!is_object($page_it)) return array();

		$actions = parent::getActions();
		if ($actions[array_pop(array_keys($actions))]['name'] != '') $actions[] = array();

        $page_it->object->setVpdContext($page_it->get('VPD'));
		$history_url = $page_it->getHistoryUrl();

		if (is_object($this->getRevisionIt()) && $this->getRevisionIt()->getId() != '') {
			$history_url .= '&start=' . $this->getRevisionIt()->getDateTimeFormat('RecordCreated');
		}
		$actions['history'] = array(
				'name' => text(824),
				'url' => $history_url,
				'uid' => 'history'
		);

		if ($this->IsWatchable($page_it)) {
			$watch_method = new WatchWebMethod($page_it);
			if ($watch_method->hasAccess()) {
				$actions[] = array();
				$actions[] = array(
						'name' => $watch_method->getCaption(),
						'url' => $watch_method->getJSCall()
				);
			}
		}
		return $actions;
	}

	function getCompareActions($object_it)
	{
	    $actions = array();
		if (!is_object($object_it)) return $actions;

		$compareIt = $this->getCompareTo();
		if ( is_object($compareIt) ) {
            if ( $object_it->getHash() != $compareIt->getHash() && $object_it->get('DocumentVersion') != $compareIt->get('DocumentVersion')  ) {
                $actions = array_merge($actions, $this->getReintegrateActions($object_it, $compareIt));
            }
        }

        if ($object_it->get('Suspected') > 0)
        {
            $ids = \TextUtils::parseIds($object_it->get('BrokenTraces'));
            if ( count($ids) < 1 ) $ids = array(0);
            $trace_it = $this->getTraceObject()->getRegistry()->Query(
                array(
                    new FilterInPredicate($ids)
                )
            );

            $ids = \TextUtils::parseIds($object_it->get('BrokenFeatures'));
            if ( count($ids) < 1 ) $ids = array(0);
            $featureTraceIt = getFactory()->getObject('pm_FunctionTrace')->getRegistry()->Query(
                array(
                    new FilterInPredicate($ids)
                )
            );

            if ($trace_it->count() + $featureTraceIt->count() < 1) return $actions;

            $method = new OpenBrokenTraceWebMethod();
            $actions[] = array(
                'name' => text(1933),
                'url' => $method->getJSCall(array('object' => $object_it->getId()))
            );
            $actions[] = array();

            while (!$trace_it->end())
            {
                if ($trace_it->get('Type') == 'branch' && $trace_it->get('UnsyncReasonType') == 'text-changed') {
                    $method = new ReintegrateWikiTraceWebMethod($trace_it->getRef('SourcePage'), $object_it);
                    if ($method->hasAccess()) {
                        $actions[] = array(
                            'url' => $method->getJSCall(
                                array(
                                    'className' => get_class($this->getObject()),
                                    'traceClass' => get_class($this->getTraceObject())
                                )
                            ),
                            'name' => $method->getCaption(),
                            'uid' => 'reintegrate'
                        );
                    }

                    $method = new IgnoreWikiLinkWebMethod($trace_it);
                    $method->setRedirectUrl("donothing");
                    $actions[] = array(
                        'url' => $method->getJSCall(),
                        'name' => $method->getCaption()
                    );
                } else {
                    $method = new ActuateWikiLinkWebMethod($trace_it);
                    $method->setRedirectUrl("donothing");
                    $actions[] = array(
                        'url' => $method->getJSCall(),
                        'name' => $method->getCaption(),
                        'uid' => 'restore-consistency'
                    );
                }
                $trace_it->moveNext();
            }

            while (!$featureTraceIt->end())
            {
                $method = new ModifyAttributeWebMethod($featureTraceIt->copy(), 'IsActual', 'Y');
                $actions[] = array(
                    'url' => $method->getJSCall(),
                    'name' => sprintf( text(3140),
                            \TextUtils::getWords($featureTraceIt
                                ->getRef('Feature')->getDisplayName(), 5)
                        )
                );
                $featureTraceIt->moveNext();
            }
        }

		return $actions;
	}

	public function getReintegrateActions($object_it, $compareIt, $postfix = '')
    {
        $actions = array();

        if ( $compareIt->getId() != '' ) {
            $method = new ReintegrateWikiTraceWebMethod($compareIt, $object_it);
            if ($method->hasAccess()) {
                $actions[] = array(
                    'url' => $method->getJSCall(
                        array(
                            'className' => get_class($this->getObject()),
                            'traceClass' => get_class($this->getTraceObject())
                        )
                    ),
                    'name' => $method->getCaption(),
                    'uid' => 'reintegrate' . $postfix
                );
            }
        }
        else {
            $method = new DeleteObjectWebMethod($object_it);
            if ($method->hasAccess()) {
                $actions[] = array(
                    'url' => $method->getJSCall(),
                    'name' => $method->getCaption()
                );
            }
        }

        return $actions;
    }

	function getDuplicateMethod($object_it)
	{
		return null;
	}

	function getTreeMenu($object_it)
	{
	    $this->sectionIt = $object_it;

		$actions = array();
		$actions[] = array(
				'name' => text(1556),
				'url' => $object_it->getUidUrl(),
				'uid' => 'open-new'
		);
		$actions[] = array();
		$actions[] = array(
				'name' => translate('Перейти'),
				'url' => "javascript: gotoRandomPage(" . $object_it->getId() . ", 4, true)"
		);

		$method = new ObjectModifyWebMethod($object_it);
		if (!$this->getReadonly() && $method->hasAccess()) {
			if ($actions[count($actions) - 1]['name'] != '') array_push($actions, array());
			$actions[] = array(
					'name' => translate('Редактировать'),
					'url' => $method->getJSCall()
			);
		}

		$method = new ObjectCreateNewWebMethod($this->getObject());
		$method->setRedirectUrl('function(jsonText){gotoPageJson(jsonText);}');
		if (!$this->getReadonly() && $method->hasAccess()) {
			if ($actions[count($actions) - 1]['name'] != '') array_push($actions, array());
			$method->setVpd($object_it->get('VPD'));
			$actions['create'] = array(
			    'uid' => 'create',
                'name' => translate('Добавить'),
                'url' => $method->url(array('ParentPage' => $object_it->getId()))
			);
		}

		$delete_actions = $this->getDeleteActions($object_it);
		if (count($delete_actions) > 0) {
			$actions[] = array();
			$actions = array_merge($actions, $delete_actions);
		}

		return $actions;
	}

	function getEditor()
	{
		$object_it = $this->getObjectIt();

		$type_it = $this->getTypeIt();

		$editor_class = is_object($object_it) ? $object_it->get('ContentEditor') : "";

		if (is_object($type_it) && $_REQUEST['PageType'] != '') {
			$type_id = $_REQUEST['PageType'];

			$type_it->moveToId($type_id);

			if ($type_it->getId() == $type_id) $editor_class = $type_it->get('WikiEditor');
		}

		$editor = WikiEditorBuilder::build($editor_class);

		if (is_object($object_it) && $_REQUEST['wiki_mode'] != 'new') {
			$editor->setObjectIt($object_it);
		} else {
			$editor->setObject($this->getObject());
		}

		return $editor;
	}

	function getTypeIt()
	{
		return null;
	}

	function getDefaultValue( $field )
	{
		$default = parent::getDefaultValue($field);

		switch( $field ) {
			case 'PageType':
				$parent = $this->getFieldValue('ParentPage');
				if ( $default == '' && $parent != '' ) {
					$parent_it = $this->getObject()->getExact($parent);
					return $parent_it->get('PageType');
				}
				break;
		}

		return $default;
	}

    function getTextTemplateIt() {
        $type_id = $_REQUEST['PageType'];
        if ( $type_id != '' ) {
            $type_it = $this->getTypeIt();
            $type_it->moveToId($type_id);
            if ( $type_it->getId() != '' && $type_it->get('DefaultPageTemplate') != '' ) {
                return $type_it->getRef('DefaultPageTemplate');
            }
        }
        return parent::getTextTemplateIt();
    }

	function getFieldValue( $field )
	{
		$object_it = $this->getObjectIt();

		switch ( $field )
		{
			case 'ContentEditor':
				return get_class( $this->getEditor() );
				
			case 'Content':
				$editor = $this->getEditor();
				// if the page was created using other editor then convert the data
				if ( is_object($object_it) && $this->getEditMode() && $object_it->get('ContentEditor') != get_class($editor) ) {
				    $editor->setObjectIt($object_it);
				    $parser = $editor->getEditorParser($object_it->get('ContentEditor'));
				    if ( is_object($parser) ) return $parser->parse($object_it->get('Content'));
				}
            case 'Caption':
            case 'SectionNumber':
                if ( !$this->getEditMode() ) {
                    $sectionIt = $this->getSectionIt();
                    return $sectionIt->get_native( $field );
                }
                break;

			case 'Template':
				$template_it = $this->getDefaultTemplateIt();
				if ( is_object($template_it) && $template_it->getId() > 0 ) {
					return $template_it->getId();
				}
				break;
		}
		return parent::getFieldValue( $field );
	}
	 
	function drawButtons()
	{
		$editor = $this->getEditor();
		
		$editor->drawPreviewButton();

		parent::drawButtons();
	}
	
	function getTransitionAttributes()
	{
		return array('Caption', 'UID');
	}

	function IsAttributeEditable($attr_name)
    {
        switch( $attr_name ) {
            case 'PageType':
                if ( $this->getReviewMode() && is_object($this->getObjectIt()) && $this->getObjectIt()->getId() != $this->getSectionIt()->getId() ) return false;
        }
        return parent::IsAttributeEditable($attr_name);
    }

    function createField( $name )
	{
		$field = parent::createField( $name );
		if ( !is_object($field) ) return $field;

		if ( $this->getReadonly() )	$field->setReadonly( true );

		switch ( $name )
		{
			case 'Caption':
				$field->setTabIndex( 1 );
				$field->setId( $field->getId().$this->form_index );

		   		if ( $this->getTransitionIt()->getId() > 0 ) {
   			        $field->setReadonly( true );
   			    }
				break;

			case 'Template':
				$field->setTabIndex( 2 );
				break;

			case 'Content':
				$field->setTabIndex( 3 );
				$field->setId( $field->getId().$this->form_index );
				$field->setSearchText($this->search_text);
				break;
		}
		return $field;
	}
	
	function createFieldObject( $name )
	{
		$objectIt = $this->getObjectIt();
		$sectionIt = $this->getSectionIt();

		switch ( $name )
		{		
			case 'Watchers':
				return new FieldWatchers( is_object($objectIt) ? $objectIt : $this->object );

			case 'Tags':
				return new FieldWikiTagTrace( is_object($sectionIt)
					? $sectionIt : null );
					
			case 'Caption':
				if ( is_object($this->getCompareTo()) ) {
					return new FieldCompareToCaption( $objectIt, $this->getCompareTo() );
				}
				
				if ( !$this->getEditMode() ) {
    				$field = new FieldTextEditable( get_class($this->getEditor()) );
     				is_object($this->getSectionIt()) ?
    					$field->setObjectIt($this->getSectionIt()) :
    						$field->setObject( $this->getObject() );
			    }
			    else {
			        $field = parent::createFieldObject($name);
			        if ( is_object($field) ) {
	 				    $field->setDefault( translate('Название') );
			        }
			    }
				return $field;
				
			case 'Content':
				if ( is_object($this->getCompareTo()) ) {
					return new FieldCompareToContent( $objectIt, $objectIt->getHtmlDecoded('Content'), $this->getCompareTo()->getHtmlDecoded('Content') );
				}
				
				$field = parent::createFieldObject($name);

 				is_object($this->getSectionIt()) ?
					$field->setObjectIt($this->getSectionIt()) :
						$field->setObject($this->getObject());

				if ( $this->getEditMode() ) {
					$field->setRows(20);
				}
				else {
					$field->setRows(2);
				}
 				return $field;
 				
			case 'PageType':
                if ( is_object($this->getTypeIt()) ) {
                    if ($this->getAction() == 'view') {
                        return new FieldReferenceAttribute(
                            $this->getObjectIt(),
                            $name,
                            $this->getObject()->getAttributeObject($name),
                            array(),
                            'btn-xs'
                        );
                    } else {
                        return new FieldDictionary( $this->getTypeIt() );
                    }
                }
                else {
                    return null;
                }

			case 'Attachments':
			    $field = new FieldWikiAttachments( is_object($objectIt) ? $objectIt : $this->getObject() );
				return $field;

			case 'ParentPage':
			    $object = $this->object->getAttributeObject($name);
		        $object->addFilter( new FilterBaseVpdPredicate() );
				$field = new FieldHierarchySelectorAppendable($object);
                $field->setCrossProject(false);
                return $field;

            case 'Dependency':
				return new FieldWikiPageDependency();
            case 'UsedBy':
            case 'IncludedIn':
			case 'Feature':
				if ( is_object($this->getObjectIt()) ) {
				    $refIt = $this->getObjectIt()->getRef($name);
				    if ( $refIt->getId() != '' ) {
                        return new FieldListOfReferences($refIt);
                    }
				}
				return null;

            case 'DocumentVersion':
                $field = new FieldAutoCompleteObject( getFactory()->getObject('Baseline') );
                $field->setAppendable();
                return $field;

            default:
				return parent::createFieldObject( $name );
		}
	}

 	function validateInputValues( $id, $action )
	{
		$message = parent::validateInputValues( $id, $action );
		
		if ( $message != '' ) return $message;
		
		// check parent page is correct
		if ( is_object($this->getObjectIt()) && $this->getAction() != 'add' )
		{
			if ( $this->getObjectIt()->getId() == $_REQUEST['ParentPage'] ) {
				return text(971);
			}
			
			$ids = $this->getObjectIt()->getAllChildrenIds();
			if ( in_array( $_REQUEST['ParentPage'], $ids ) ) {
				return text(971);
			}
		}
		
		return '';
	}
		
	function getRenderParms()
	{
		$structureActions = array();
		$object = $this->getObject();
		$object_it = $this->getObjectIt();

        if ( is_object($object_it) && $object_it->IsPersisted() )
		{
			$attachments = new FieldWikiAttachments( $object_it->copy() );
            $attachments->setButtonTemplate('pm/WikiPageMoreAttachmentButton.php');
            $attachments->setReadonly( !$this->checkAccess() || $this->getReadonly() );
			$attachments->setEditMode( false );

            $structureActions = $this->getStructureActions( $this->getSectionIt() );
        }

        $parms = array (
			'lifecycle' => is_object($object_it) && is_a($object_it, 'StatableIterator') ? $object_it->IsTransitable() : false,
			'formurl' =>  $object->getPage(),
		    'index' => $this->form_index,
		    'document_mode' => $this->getReviewMode(),
			'baseline' => is_object($this->getRevisionIt()) ? $this->getRevisionIt()->getId() : '',
			'compare_actions' => $this->getCompareActions($object_it),
			'persisted' => is_object($object_it) ? $object_it->IsPersisted() : false,
            'attachments' => $attachments,
            'structureActions' => $structureActions,
			'trace_attributes' =>
				array_merge(
					$this->getObject()->getAttributesByGroup('trace'),
					$this->getObject()->getAttributesByGroup('source-attribute')
				)
		);

		$parent_parms = parent::getRenderParms();

		if ( count($this->attributesVisibility) > 0 ) {
            foreach( $parent_parms['attributes'] as $key => $attributeData ) {
                if ( !array_key_exists($key, $this->attributesVisibility) ) continue;
                $parent_parms['attributes'][$key]['visible'] = $this->attributesVisibility[$key];
            }
        }

		return array_merge($parent_parms , $parms );
	}

	function getStructureActions( $object_it, $parms = array() )
	{
		$actions = array();

        if ( !$this->getReadonly() )
        {
            $defaultTypeIt = $this->getTypeIt();
            if (is_object($defaultTypeIt)) {
                while (!$defaultTypeIt->end()) {
                    if ($defaultTypeIt->get('IsDefault') == 'Y') break;
                    $defaultTypeIt->moveNext();
                }
                if ($defaultTypeIt->getId() == '') $defaultTypeIt = null;
            }

            if ($object_it->get('TotalCount') < 1 && $object_it->get('ParentPage') != '') {
                $new_sibling_method = new DocNewChildWebMethod($this->getObject());
                $new_sibling_method->setVpd($object_it->get('VPD'));

                $parent_id = $object_it->get('ParentPage') != ''
                    ? $object_it->get('ParentPage')
                    : $object_it->getId();

                $sort_index = $object_it->get('OrderNum') + 1;

                $actions['sibling'] = array(
                    'name' => $this->getNewSiblingActionName(),
                    'url' => $new_sibling_method->url(
                        array_merge(
                            $parms, array(
                                'ParentPage' => $parent_id,
                                'OrderNum' => $sort_index,
                                'PageType' => is_object($defaultTypeIt)
                                    ? $defaultTypeIt->getId() : $object_it->get('PageType')
                            )
                        )
                    ),
                    'icon' => 'icon-resize-vertical',
                    'uid' => 'new-sibling'
                );
            }

            $new_child_method = new DocNewChildWebMethod($this->getObject());
            $new_child_method->setVpd($object_it->get('VPD'));

            $actions['child'] = array(
                'name' => $this->getNewChildActionName(),
                'url' => $new_child_method->url(
                    array_merge(
                        $parms, array(
                            'ParentPage' => $object_it->getId(),
                            'OrderNum' => 1,
                            'PageType' => is_object($defaultTypeIt) ? $defaultTypeIt->getId() : $object_it->get('PageType')
                        )
                    )
                ),
                'icon' => 'icon-resize-horizontal',
                'uid' => $object_it->get('ParentPage') == '' ? 'new-sibling' : 'new-child'
            );
        }

        $attachments_method = new ObjectModifyWebMethod($object_it);
        $actions['history'] = array (
            'name' => text(3301),
            'url' => $attachments_method->getJSCall(
                array_merge(
                    $parms, array('tab'=>'pmlastchangessection')
                )
            ),
            'icon' => 'icon-time',
            'uid' => 'page-history'
        );

        if ( !$this->getReadonly() )
        {
            $actions['files'] = array (
                'name' => translate('файлы'),
                'icon' => 'icon-file',
                'uid' => 'attach-files'
            );

            $attachments_method = new ObjectModifyWebMethod($object_it);
            $actions['tags'] = array (
                'name' => text(2082),
                'url' => $attachments_method->getJSCall(
                    array_merge(
                        $parms, array('tab'=>'additional')
                    )
                ),
                'icon' => 'icon-tags',
                'uid' => 'new-tag-file'
            );
        }

        $actions['comments'] = array (
            'name' => translate('комментарии'),
            'url' => $attachments_method->getJSCall(
                array_merge(
                    $parms, array('tab'=>'comments')
                )
            ),
            'icon' => 'icon-comment',
            'uid' => 'new-comment'
        );

		return $actions;
	}

	function getNewChildActionName() {
	    return text(2091);
    }

    function getNewSiblingActionName() {
        return text(2092);
    }

	function getTemplate()
	{
        if ( $_REQUEST['attributesonly'] != '' ) return "core/PageFormDetails.php";
	    if( !$this->getEditMode() ) return "pm/WikiPageForm.php";
	    return parent::getTemplate();
	}

	function getSourceIt()
	{
	    $result = array();
		if ( $_REQUEST['Request'] != '' ) {
            $result[] = array (
				getFactory()->getObject('Request')->getExact($_REQUEST['Request']),
				'Description'
			);
            return array_merge(parent::getSourceIt(), $result);
		}
		if ( $_REQUEST['Task'] != '' ) {
			$task_it = getFactory()->getObject('Task')->getExact($_REQUEST['Task']);
			if ( $task_it->get('ChangeRequest') == '' ) return parent::getSourceIt();
            $result[] = array (
				$task_it->getRef('ChangeRequest'),
				'Description'
			);
            return array_merge(parent::getSourceIt(), $result);
		}
		if ( $_REQUEST['Requirement'] != '' ) {
			$req = getFactory()->getObject('Requirement');
			if ( $_REQUEST['Baseline'] != '' ) {
				$req->addPersister(
						new SnapshotItemValuePersister($_REQUEST['Baseline'])
				);
			}
            $result[] = array ($req->getExact($_REQUEST['Requirement']),'WikiIteratorExportHtml');
		}
		return array_merge(parent::getSourceIt(), $result);
	}

    function getShortAttributes()
    {
        return array_merge(
            parent::getShortAttributes(),
            array('PageType', 'Template', 'Importance')
        );
    }

    function getTaskTypesRelated()
    {
        return array();
    }

    function persistTemporaryAttachments( $service, $objectIt ) {
        $service->attachTemporaryFiles($objectIt, 'Content', getFactory()->getObject('WikiPageFile'));
    }

    function setAttributesVisibility( $data ) {
	    $this->attributesVisibility = $data;
    }
}
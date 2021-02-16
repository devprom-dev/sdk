<?php
use Devprom\ProjectBundle\Service\Email\CommentNotificationService;
use Devprom\ProjectBundle\Service\Files\UploadFileService;
use Devprom\ProjectBundle\Service\Model\ModelChangeNotification;
include_once SERVER_ROOT_PATH."pm/classes/workflow/WorkflowStateAttributesModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/workflow/WorkflowTransitionAttributesModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/model/validators/ModelProjectValidator.php";
include_once SERVER_ROOT_PATH."pm/views/comments/FieldCheckNotifications.php";
include_once SERVER_ROOT_PATH."pm/classes/common/CustomAttributesModelBuilder.php";
include "FieldWidgetUrl.php";
include "FieldState.php";
include "FieldComputed.php";
include "FieldUID.php";
include "JSONViewerField.php";

class PMPageForm extends PageForm
{
    private $customtypes = array();
    private $customkinds = array();
    private $customdefault = array();
    private $templateFields = array();
    private $allowChooseProject = false;

    function getId()
    {
    	return parent::getId().$this->getTransitionIt()->getId();
    }

    function setObjectIt( $object_it )
    {
        $this->state_it = null;
        parent::setObjectIt($object_it);
    }

    function getIterator( $objectId )
    {
        if ( is_numeric($objectId) && $objectId > 0 ) {
            $objectIt = $this->getObject()->createCachedIterator(
                array(
                    array(
                        $this->getObject()->getIdAttribute() => $objectId
                    )
                )
            );
            $builder = new CustomAttributesModelBuilder($objectIt);
            $builder->build($this->getObject());
        }
        return parent::getIterator($objectId);
    }

    protected function extendModel()
    {
        $entities = TextTemplateEntityRegistry::getEntities();
        $this->templateFields = preg_split('/,/',$entities[get_class($this->getObject())]);

        $shareable = in_array(
            strtolower(get_class($this->getObject())),
            getFactory()->getObject('SharedObjectSet')->getAll()->fieldToArray('ClassName')
        );

        $this->allowChooseProject = $shareable && (
                getSession()->getProjectIt()->IsPortfolio()
                || getSession()->getProjectIt()->IsProgram()
            );

        if ( in_array($this->getMode(), array('new','add')) && $this->allowChooseProject ) {
            if ( $this->getObject()->getAttributeEditable('Project') ) {
                $this->getObject()->setAttributeVisible('Project', true);
            }
            else {
                $this->getObject()->addAttribute('Project', 'REF_ProjectActiveId', translate('Проект'), true, false);
            }
            $this->getObject()->setAttributeRequired('Project', true);
        }

        if ( !is_object($this->getObjectIt()) && $this->getObject() instanceof MetaobjectStatable && !$this->allowChooseProject ) {
            $this->getObject()->setAttributeVisible('State', true);
        }

        $this->buildCustomAttributes();

        if ( is_object($this->getObjectIt()) ) {
            $uid = new ObjectUID();
            if ( $_REQUEST['formonly'] != '' && $uid->hasUid($this->getObjectIt()) ) {
                if ( $this->getObject()->IsAttributeStored('UID') ) {
                    $this->getObject()->setAttributeVisible('UID', true);
                    $this->getObject()->setAttributeOrderNum('UID', 100);
                }
                else {
                    $this->getObject()->addAttribute('UID', 'VARCHAR', 'UID', true, false);
                }
            }
        }

        // extend model depends on workflow settings (eg, required attributes)
        $transition_it = $this->getTransitionIt();
        if ( is_object($this->getObjectIt()) && $transition_it->getId() > 0 )
        {
        	$model_builder = new WorkflowTransitionAttributesModelBuilder(
       			$transition_it,
                $this->getTransitionAttributes(),
                array_merge(
                    $this->getObjectIt()->getData(),
                    $_REQUEST
                )
    		);
        }
        else
        {
            if ( !is_object($this->getObjectIt()) ) {
                $this->getObject()->setAttributeVisible('IntegrationLink', false);

                $state_it = $this->getStateIt();
                if ( $_REQUEST['State'] != '' ) {
                    $state_it->moveTo('ReferenceName', trim($_REQUEST['State']));
                }
                $model_builder = new WorkflowStateAttributesModelBuilder(
                    $state_it, array()
                );
                foreach( $this->customtypes as $attribute => $type ) {
                    if ( $type == 'computed' ) {
                        $this->getObject()->setAttributeVisible($attribute, false);
                    }
                }
            }
            else {
                $this->getObject()->setAttributeVisible('IntegrationLink', $this->getObjectIt()->get('IntegrationLink') != '');

                if ( !$this->getEditMode() ) {
                    foreach( $this->customtypes as $attribute => $type ) {
                        if ( $this->customkinds[$attribute] != '' ) {
                            $visible = $this->customkinds[$attribute] == $this->getDiscriminator();
                            $this->getObject()->setAttributeVisible($attribute, $visible);
                        }
                    }
                }

                $model_builder = new WorkflowStateAttributesModelBuilder(
                    $this->getStateIt(), array()
                );
            }
        }

        $model_builder->build( $this->getObject() );
    }
    
	protected function buildCustomAttributes()
	{
		if ( !getFactory()->getObject('CustomizableObjectSet')->checkObject($this->getObject()) ) return;

        $it = getFactory()->getObject('pm_CustomAttribute')->getByEntity($this->getObject());
        while (!$it->end())
        {
            $this->customtypes[$it->get('ReferenceName')] = $it->getRef('AttributeType')->get('ReferenceName');
            $this->customdefault[$it->get('ReferenceName')] = $it->get('DefaultValue');

            if ($it->get('ObjectKind') != '') {
            	$this->customkinds[$it->get('ReferenceName')] = $it->get('ObjectKind');
            }

            $it->moveNext();
        }
	}

    function getValidators() {
        return array_merge(
            parent::getValidators(),
            array(
                new ModelProjectValidator()
            )
        );
    }

    function persist()
    {
        // unset values defined for other kinds of entity
        foreach( $this->customkinds as $attribute => $value ) {
            if ( $this->getDiscriminator() != $value ) {
                unset($_REQUEST[$attribute]);
            }
        }

    	if ( !parent::persist() ) return false;

    	$object_it = $this->getObjectIt();

        $service = new UploadFileService();
        $service->deleteFiles();
        $this->persistTemporaryAttachments($service, $object_it);

    	$invoke_workflow = is_object($object_it) 
    		&& ($this->getAction() == 'add' || $this->getAction() == 'modify' && $this->getTransitionIt()->getId() > 0);
    	
	    if ( $invoke_workflow )
	    {
            $it = $object_it->object->getExact($object_it->getId());
	        $data = array();

	        foreach( $it->getData() as $key => $value ) {
	            if ( $this->getObject()->IsAttributeVisible($key) ) {
                    $data[$key] = $value;
                }
            }
	    	getFactory()->getEventsManager()
                ->executeEventsAfterBusinessTransaction(
                    $it, 'WorklfowMovementEventHandler', $data
                );
	    }
	    
	    return true;
    }

    function persistTemporaryAttachments( $service, $objectIt ) {
        $service->attachTemporaryFiles($objectIt, 'File', getFactory()->getObject('pm_Attachment'));
    }

    function getDiscriminator()
    {
        $field = $this->getDiscriminatorField();
        if ( $field == '' ) return '';

        if ( $_REQUEST[$field] > 0 ) {
            return $this->getObject()->getAttributeObject($field)->getExact($_REQUEST[$field])->get('ReferenceName');
        }
        elseif( is_object($this->getObjectIt()) ) {
            return $this->getObjectIt()->getRef($field)->get('ReferenceName');
        }
    }
    
 	function getDiscriminatorField()
 	{
 		return '';
 	}

    function getSite()
    {
        return 'pm';
    }
    
    function getStateIt()
    {
    	if ( is_object($this->state_it) ) return $this->state_it;
    	if ( ! $this->getObject() instanceof MetaobjectStatable ) {
    		return $this->state_it = getFactory()->getObject('StateBase')->getEmptyIterator(); 
    	}
        if ( ! class_exists($this->getObject()->getStateClassName()) ) {
    		return $this->state_it = getFactory()->getObject('StateBase')->getEmptyIterator(); 
    	}
    	
    	$object_it = $this->getObjectIt();
    	if ( is_object($object_it) ) {
    	    return $this->state_it = $object_it->getStateIt();
        }
        else {
            return $this->state_it = \WorkflowScheme::Instance()->getStateIt($this->getObject());
        }
    }

    function IsAttributeVisible( $attr )
    {
        switch( $attr )
        {
            default:
                if ( $this->getObject()->IsReference($attr) ) {
                    if ( !getFactory()->getAccessPolicy()->can_read($this->getObject()->getAttributeObject($attr)) ) {
                        return false;
                    }
                }
                return parent::IsAttributeVisible( $attr );
        }
    }

 	function IsAttributeRequired( $attr )
 	{
 		if ( array_key_exists( $attr, $this->customkinds ) )
 		{
 			$discriminator = $this->getDiscriminator();
 			if ( $discriminator != $this->customkinds[$attr] ) return false;
 		}
		
 		return parent::IsAttributeRequired( $attr );
 	}

    function getDefaultValue( $field )
    {
        $value = parent::getDefaultValue( $field );

        switch( $field ) {
            case 'Project':
                if ( $value == '' ) return getSession()->getProjectIt()->getId();
                break;
            default:
                if ( array_key_exists( $field, $this->customdefault ) && $this->getEditMode() ) {
                    if ( $this->getDiscriminator() == $this->customkinds[$field] ) {
                        $value = $this->customdefault[$field];
                    }
                }
                if ( $value == '' && in_array($field, $this->templateFields) && $this->getObject()->IsAttributeVisible($field) ) {
                    $template_it = $this->getTextTemplateIt();
                    if ( $template_it->getId() != '' ) {
                        return $template_it->getHtmlDecoded('Content');
                    }
                }
        }

        return $value;
    }

    function getTextTemplateIt() {
        return getFactory()->getObject('TextTemplate')->getRegistry()->Query(
            array (
                new FilterVpdPredicate(),
                new TextTemplateEntityPredicate(get_class($this->getObject())),
                new FilterAttributePredicate('IsDefault', 'Y')
            )
        );
    }

    function createFieldObject($attr)
    {
        switch ($attr) 
        {
            case 'UID':
                if ( is_object($this->getObjectIt()) ) {
                    return new FieldUID($this);
                }
                else {
                    return parent::createFieldObject($attr);
                }

            case 'Project':
                if ( $this->allowChooseProject ) {
                    return new FieldAutoCompleteObject(getFactory()->getObject('ProjectLinkedActive'));
                }
                return parent::createFieldObject($attr);

            case 'State':
                if ( $this->getObject() instanceof MetaobjectStatable ) {
                    $field = new FieldState(getFactory()->getObject($this->getObject()->getStateClassName()));
                    $field->setInstantiationAllowedOnly(true);
                    return $field;
                }
                else {
                    return parent::createFieldObject($attr);
                }

            case 'TransitionNotification':
                $field = new FieldCheckNotifications();
                if ( is_object($this->getObjectIt()) ) {
                    $options = new CommentNotificationService($this->getObjectIt());
                    $field->setEmails($options->getEmails());
                    $field->setPrivate($options->getPrivate($this->getTransitionIt()));
                }
                return $field;

            case 'Caption':
                if ( !$this->getEditMode() ) {
                    $field = new FieldTextEditable();
                    $field->setObjectIt( $this->getObjectIt() );
                }
                else {
                    $field = parent::createFieldObject($attr);
                }
                return $field;

            default:
                $attributeGroups = $this->getObject()->getAttributeGroups($attr);
                if ( in_array('dictionary', $attributeGroups) ) {
                    if ( $this->getAction() == 'view' && !in_array('multiselect', $attributeGroups) ) {
                        return new FieldReferenceCustomAttribute(
                            $this->getObjectIt(),
                            $attr,
                            new PMCustomDictionary($this->getObject(), $attr),
                            array(),
                            true
                        );
                    }
                    else {
                        return new FieldCustomDictionary(
                            is_object($this->getObjectIt()) ? $this->getObjectIt() : $this->getObject(),
                            $attr
                        );
                    }
                }

                if ( in_array('computed', $attributeGroups) && is_object($this->getObjectIt()) ) {
                    return new FieldComputed($this->getObjectIt(), $attr);
                }

                if ( $this->getObject()->getAttributeType($attr) == 'wysiwyg')
                {
                    if ( $attr == "Description" && is_object($this->getObjectIt()) && !$this->getEditMode() ) {
                        if ( json_decode(JSONViewerField::stripTags($this->getObjectIt()->get($attr))) ) {
                            return new JSONViewerField();
                        }
                    }

                    $field = new FieldWYSIWYG();

                    $object_it = $this->getObjectIt();
                    is_object($object_it) ? $field->setObjectIt($object_it)
                            : $field->setObject($this->getObject());

                    if ( $this->IsAttributeEditable($attr) ) {
                        $field->getEditor()->setMode( WIKI_MODE_NORMAL );
                        $field->setHasBorder(false);
                    }
                    else {
                        $field->setCssClassName( 'wysiwyg-text' );
                    }
                    $field->setToolbar(WikiEditorBase::ToolbarFull);
                    return $field;
                }

                return parent::createFieldObject($attr);
        }
    }

    function createField($name)
    {
        $field = parent::createField($name);
        if ( !is_object($field) ) return $field;

        switch( $name ) {
            case 'IntegrationLink':
                $field->setReadOnly(true);
                break;
            case 'TransitionComment':
                $field->setObjectIt(getFactory()->getObject('Comment')->getEmptyIterator());
                break;
        }

        if ( $this->customtypes[$name] == 'computed' ) {
            $field->setReadOnly(true);
        }
        return $field;
    }

    function getFieldDescription($field_name)
    {
        $description = parent::getFieldDescription($field_name);
        switch( $field_name ) {
            default:
                if ( $description == '' && $this->getEditMode() && $this->getObject()->getAttributeType($field_name) == 'wysiwyg' ) {
                    $description = str_replace('%1', getFactory()->getObject('Module')->getExact('dicts-texttemplate')->getUrl(), text(606));
                }

                $groups = $this->getObject()->getAttributeGroups($field_name);
                if ( in_array('dictionary', $groups) ) {
                    $it = getFactory()->getObject('pm_CustomAttribute')->getByEntity($this->getObject());
                    $it->moveTo('ReferenceName', $field_name);
                    $method = new ObjectModifyWebMethod($it);
                    if ( $method->hasAccess() ) {
                        $description .= ' '.str_replace('%1', $method->getJSCall(), text(2183));
                    }
                }
                return $description;
        }
    }

    function getTransitionAttributes()
	{
		return array();
	}
	
    function getShortAttributes()
    {
        return array_intersect(
            array_keys($this->getObject()->getAttributes()),
            array('State', 'Project')
        );
    }

    function getRenderParms()
    {
        $uid = new ObjectUID;

 		$object_it = $this->getObjectIt();
        $nextIt = $this->getNextObjectIt();
        if ( $nextIt->getId() != '' ) {
            $nextInfo = $uid->getUIDInfo($nextIt);
            $nextUrl = $nextInfo['url'];
            $nextTitle = $nextInfo['uid'] . ' ';

            $caption = $nextIt->getDisplayName();
            $items = explode(' ', $caption);
            if ( count($items) > 7 ) {
                $nextTitle .= join(' ', array_slice($items, 0, 7)) . '...';
            }
            else {
                $nextTitle .= $caption;
            }
        }

        return array_merge(parent::getRenderParms(), array(
            'state_name' => is_object($object_it) && is_a($object_it, 'StatableIterator') && $object_it->IsTransitable()
                                ? $object_it->getStateIt()->get('Caption')
                                : "",
            'form_class' => '',
            'showtabs' => $this->getTransitionIt()->getId() == '',
            'shortAttributes' => $this->getShortAttributes(),
            'nextUrl' => $nextUrl,
            'nextTitle' => $nextTitle,
            'listWidgetIt' => getFactory()->getObject('ObjectsListWidget')
                                ->getByRef('Caption', get_class($this->getObject()))->getWidgetIt()
        ));
    }
    
	function drawScripts()
	{
	    parent::drawScripts();
        $discriminatorField = $this->getDiscriminatorField();
	    if ( $this->getEditMode() && $discriminatorField != '' )
	    {
    	?>
    	<script type="text/javascript">
            if ( typeof completeUICustomFields != 'undefined' ) {
                completeUICustomFields('<?=$this->getId()?>','*[name=\'<?=$discriminatorField?>\']', <?=json_encode(array_keys($this->customkinds))?>, <?=json_encode(array_values($this->customkinds))?>);
            }
    	</script>
    	<?php
	    }
	}

    function getHintId() {
        if ( $this->getTransitionIt()->getId() != '' ) {
            return parent::getHintId().'State';
        }
        else {
            return parent::getHintId();
        }
    }

 	function getHint()
	{
        $hint = parent::getHint();
		if ( $this->getTransitionIt()->getId() != '' )
		{
			$method = new ObjectModifyWebMethod($this->getTransitionIt());
			$method->setObjectUrl(
					getSession()->getApplicationUrl().'project/workflow/'.$this->getObject()->getStateClassName().$this->getTransitionIt()->getEditUrl()
				);
			$method_state = new ObjectModifyWebMethod(
			    $this->getTransitionIt()->getRef('TargetState', $this->getStateIt()->object)
            );
            $hint = str_replace('%1', $method->getJsCall(), str_replace('%2', $method_state->getJsCall(), text(2020)));
		}
		return $this->parseHint($hint);
	}

    function parseHint( $text )
    {
        $text = preg_replace('/\%project\%/i', getSession()->getProjectIt()->get('CodeName'), $text);
        $text = preg_replace('/&lt;auth-key&gt;/i', \AuthenticationAPIKeyFactory::getAuthKey(getSession()->getUserIt()), $text);

        if ( $this->getObject() instanceof MetaobjectStatable ) {
            $method = new ObjectModifyWebMethod($this->getStateIt());
            if ( $method->hasAccess() ) {
                $url = $method->getJSCall();
            }
            else {
                $url = getFactory()->getObject('Module')->
                            getExact('workflow-'.strtolower($this->getObject()->getStateClassName()))->getUrl();
            }
            $text = preg_replace('/%form:state-url%/', $url, $text);
            }

        return $text;
    }

    protected function getComputedFields()
    {
        return array_keys(array_filter($this->customtypes, function($value) {
            return $value == 'computed';
        }));
    }

    function redirectOnDelete($object_it, $redirect_url = '')
    {
        $method = new UndoWebMethod(ChangeLog::getTransaction());
        $method->setCookie();

        parent::redirectOnDelete($object_it, $redirect_url);
    }

    protected function getNeighbourAttributes()
    {
        return array();
    }

    protected function getNeighbourIt( $objectIt )
    {
        $attributes = $this->getNeighbourAttributes();
        if ( count($attributes) < 1 ) return $this->getObject()->getEmptyIterator();

        $filters = array(
            new FilterVpdPredicate()
        );
        $sorts = array();
        foreach( $attributes as $attribute ) {
            if ( !$this->getObject()->hasAttribute($attribute) ) continue;
            $clause = new SortAttributeClause($attribute);
            $clause->setNullOnTop(false);
            $sorts[] = $clause;
            $filters[] = new FilterAttributePredicate($attribute, $objectIt->get($attribute));
        }
        $sorts[] = new SortOrderedClause();
        $sorts[] = new SortKeyClause();

        $registry = $this->getObject()->getRegistry();
        $registry->setLimit(1);
        $registry->setPersisters(array(
            new EntityProjectPersister()
        ));

        $resultIt = $registry->Query(
            array_merge($filters, $sorts, array(
                new FilterNextSiblingsPredicate($objectIt),
                new FilterNextKeyPredicate($objectIt)
            ))
        );
        if ( $resultIt->getId() != '' ) return $resultIt;

        $resultIt = $registry->Query(
            array_merge($filters, $sorts, array(
                new FilterNotInPredicate($objectIt->getId())
            ))
        );
        if ( $resultIt->getId() != '' ) return $resultIt;

        $resultIt = $registry->Query(
            array_merge($sorts, array(
                new FilterVpdPredicate(),
                new FilterNextSiblingsPredicate($objectIt),
                new FilterNextKeyPredicate($objectIt)
            ))
        );
        if ( $resultIt->getId() != '' ) return $resultIt;

        return $registry->Query(
            array_merge($sorts, array(
                new FilterVpdPredicate(),
                new FilterNotInPredicate($objectIt->getId())
            ))
        );
    }

    protected function getNextObjectIt()
    {
        $objectIt = $this->getObjectIt();
        if ( !is_object($objectIt) ) return $this->getObject()->getEmptyIterator();
        return $this->getNeighbourIt($objectIt);
    }

    function render( $view, $parms )
    {
        $service = new ModelChangeNotification();
        $service->clearUser($this->getObjectIt(), getSession()->getUserIt(), array('commented'));
        parent::render( $view, $parms );
    }

    private $state_it = null;
}
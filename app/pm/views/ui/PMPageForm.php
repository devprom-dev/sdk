<?php

include_once SERVER_ROOT_PATH."pm/classes/workflow/WorkflowStateAttributesModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/workflow/WorkflowTransitionAttributesModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/model/validators/ModelProjectValidator.php";
include "FieldWidgetUrl.php";
include "FieldUID.php";
include "FieldListOfReferences.php";

///////////////////////////////////////////////////////////////////////////////
class PMPageForm extends PageForm
{
    private $customtypes = array();
    private $customkinds = array();
    private $customdefault = array();

    function PMPageForm($object)
    {
        parent::__construct($object);
    }
    
    function getId()
    {
    	return parent::getId().$this->getTransitionIt()->getId();
    }
    
    protected function extendModel()
    {
        if ( in_array($this->getMode(), array('new','add')) && getSession()->getProjectIt()->IsPortfolio() ) {
            $this->getObject()->setAttributeRequired('Project', true);
            $this->getObject()->setAttributeVisible('Project', true);
        }

        $this->buildCustomAttributes();

        // extend model depends on workflow settings (eg, required attributes)
        $transition_it = $this->getTransitionIt();

        if ( $transition_it->getId() > 0 )
        {
        	$model_builder = new WorkflowTransitionAttributesModelBuilder(
        			$transition_it, $this->getTransitionAttributes()
    		);
        }
        else
        {
            if ( !is_object($this->getObjectIt()) )
            {
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

    function getModelValidator()
    {
        $validator = parent::getModelValidator();
        $validator->addValidator( new ModelProjectValidator() );
        return $validator;
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

    	$invoke_workflow = is_object($object_it) 
    		&& ($this->getAction() == 'add' || $this->getAction() == 'modify' && $this->getTransitionIt()->getId() > 0);
    	
	    if ( $invoke_workflow )
	    {
	    	getFactory()->getEventsManager()
	    			->executeEventsAfterBusinessTransaction(
	    					$object_it->object->getExact($object_it->getId()), 'WorklfowMovementEventHandler');
	    }
	    
	    return true;
    }

    function validateInputValues($id, $action)
    {
        $result = parent::validateInputValues($id, $action);

        if ($result != '') return $result;

        $object = $this->getObject();
        
        $attribute = getFactory()->getObject('pm_CustomAttribute');
        
        $it = $attribute->getByEntity($object);

        $unique_attrs = array();
        
        $parms = $_REQUEST;
        
		$mapper = new ModelDataTypeMapper();
	    
		$mapper->map($object, $parms);
        
        while (!$it->end()) 
        {
            $value = $parms[$it->get('ReferenceName')];

            if ($it->get('IsUnique') == 'Y' && $value != '') 
            {
                $unique_attrs[] = array(
                        'ReferenceName' => $it->get('ReferenceName'),
                        'AttributeType' => $it->get('AttributeType'),
                        'Id' => $it->getId(),
                        'DisplayName' => $it->getDisplayName(),
                		'AttributeValue' => strtolower($value)
                );
            }
            
            $it->moveNext();
        }

        $value_object = getFactory()->getObject('pm_AttributeValue');
        
        foreach ($unique_attrs as $key => $attr) 
        {
            $field = $attribute->getAttributeObject('AttributeType')->getExact($attr['AttributeType'])->getValueColumn();

            $attr_it = $value_object->getByRefArray( array(
                		'CustomAttribute' => $attr['Id'],
                        'LCASE(' . $field . ')' => $attr['AttributeValue']
            ));

            if ($attr_it->count() > 0 && $attr_it->get('ObjectId') != $id) 
            {
            	return str_replace('%1', $attr['DisplayName'], text(1176));
            }
        }

        return $result;
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
    	if ( is_object($object_it) ) return $this->state_it = $object_it->getStateIt();

    	return $this->state_it = getFactory()->getObject('StateBase')->getEmptyIterator();
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

        if ( array_key_exists( $field, $this->customdefault ) && $this->getEditMode() ) {
            if ( $this->getDiscriminator() == $this->customkinds[$field] ) {
                $value = $this->customdefault[$field];
            }
        }

        return $value;
    }

    function createFieldObject($attr)
    {
        switch ($attr) 
        {
            case 'UID':
                return new FieldUID($this->getObjectIt());

            case 'Project':
                if ( getSession()->getProjectIt()->IsPortfolio() ) {
                    return new FieldAutoCompleteObject(getFactory()->getObject('ProjectLinked'));
                }
                else {
                    return parent::createFieldObject($attr);
                }

            default:
                foreach ($this->customtypes as $refname => $type) 
                {
                    if ($attr == $refname && $type == 'dictionary') {
                        return new FieldCustomDictionary($this->getObject(), $refname);
                    }
                }

                if ( $this->getObject()->getAttributeType($attr) == 'wysiwyg')
                {
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
                    return $field;
                }
    
                return parent::createFieldObject($attr);
        }
    }

    function createField($name)
    {
        $field = parent::createField($name);
        switch( $name ) {
            case 'IntegrationLink':
                $field->setReadOnly(true);
                break;
        }

        if ( $this->customtypes[$name] == 'computed' ) {
            $field->setReadOnly(true);
        }
        return $field;
    }

    function getTransitionAttributes()
	{
		return array();
	}
	
    function process()
    {
        $this->extendModel();
        
        return parent::process();
    }
	
    function getRenderParms()
    {
        $this->extendModel();
    	
 		$object_it = $this->getObjectIt();

        return array_merge(parent::getRenderParms(), array(
            'state_name' => is_object($object_it) && is_a($object_it, 'StatableIterator') && $object_it->IsTransitable() ? $object_it->getStateName() : ""
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
		if ( $this->getTransitionIt()->getId() != '' )
		{
			$method = new ObjectModifyWebMethod($this->getTransitionIt());
			$method->setObjectUrl(
					getSession()->getApplicationUrl().'project/workflow/'.$this->getObject()->getStateClassName().$this->getTransitionIt()->getEditUrl()
				);
			$method_state = new ObjectModifyWebMethod($this->getTransitionIt()->getRef('TargetState'));
			return $this->parseHint(
                str_replace('%1', $method->getJsCall(),
						str_replace('%2', $method_state->getJsCall(), text(2020)))
            );
		}
		return $this->parseHint(parent::getHint());
	}

    function parseHint( $text )
    {
        $text = preg_replace('/\%project\%/i', getSession()->getProjectIt()->get('CodeName'), $text);
        return $text;
    }

    protected function getComputedFields()
    {
        return array_keys(array_filter($this->customtypes, function($value) {
            return $value == 'computed';
        }));
    }

    private $state_it = null;
}
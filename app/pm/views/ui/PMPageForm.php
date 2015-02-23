<?php

include_once SERVER_ROOT_PATH."pm/classes/workflow/WorkflowStateAttributesModelBuilder.php";
include_once SERVER_ROOT_PATH."pm/classes/workflow/WorkflowTransitionAttributesModelBuilder.php";

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
    
    protected function extendModel()
    {
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
        	$model_builder = new WorkflowStateAttributesModelBuilder(
        			$this->getStateIt(), 
        			!is_object($this->getObjectIt()) ? $this->getNewObjectAttributes() : array()
    		);
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

            $this->customdefault[$it->get('ReferenceName')] = true;
            
            if ($it->get('ObjectKind') != '')
            {
            	$this->customkinds[$it->get('ReferenceName')] = $it->get('ObjectKind');
            }

            $it->moveNext();
        }
	}
	
    function persist()
    {
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
        return '';
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
    		
    	if ( ! $this->getObject() instanceof MetaobjectStatable )
    	{
    		return $this->state_it = getFactory()->getObject('StateBase')->getEmptyIterator(); 
    	}

        if ( ! class_exists($this->getObject()->getStateClassName()) )
    	{
    		return $this->state_it = getFactory()->getObject('StateBase')->getEmptyIterator(); 
    	}
    	
    	$object_it = $this->getObjectIt();
    	
    	if ( is_object($object_it) ) return $this->state_it = $object_it->getStateIt();

    	return $this->state_it = 
    			getFactory()->getObject($this->getObject()->getStateClassName())->
		    			getRegistry()->Query(
				    			array (
				    					new FilterBaseVpdPredicate(),
				    					new SortOrderedClause()
				    			)
				    	);
    }
    
 	function IsAttributeVisible( $attr )
 	{
 		if ( array_key_exists( $attr, $this->customkinds ) )
 		{
 			if ( $this->getDiscriminator() != $this->customkinds[$attr] ) return false;
 		}
 		
 		return parent::IsAttributeVisible( $attr );
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

    function getFieldValue( $field )
	{
	    $value = parent::getFieldValue( $field );
	    
 		if ( $value == '' && array_key_exists( $field, $this->customdefault ) && $this->getEditMode() )
 		{
 		    $value = $this->object->getDefaultAttributeValue( $field );
 		}
 		
 		return $value;
	}
	    
    function createFieldObject($attr)
    {
        switch ($attr) 
        {
            default:
                foreach ($this->customtypes as $refname => $type) 
                {
                    if ($attr == $refname && $type == 'dictionary') 
                    {
                        return new FieldCustomDictionary($this->getObject(), $refname);
                    }
    
                    if ($attr == $refname && $type == 'wysiwyg') 
                    {
                        $field = new FieldWYSIWYG();
    
                        $object_it = $this->getObjectIt();
    
                        is_object($object_it) ? $field->setObjectIt($object_it)
                                : $field->setObject($this->getObject());
    
                        $editor = $field->getEditor();
    
                        $editor->setMode( WIKI_MODE_MINIMAL | WIKI_MODE_INLINE );
    
                        $field->setCssClassName( 'wysiwyg-field' );
                        
                        return $field;
                    }
                }

                if ( $this->getObject()->getAttributeType($attr) == 'wysiwyg')
                {
                    $field = new FieldWYSIWYG();

                    $object_it = $this->getObjectIt();

                    is_object($object_it) ? $field->setObjectIt($object_it)
                            : $field->setObject($this->getObject());

                    $editor = $field->getEditor();

					//$field->setHasBorder( false );
					//$field->getEditor()->setMode( WIKI_MODE_NORMAL );
                    $editor->setMode( WIKI_MODE_MINIMAL | WIKI_MODE_INLINE );

                    $field->setCssClassName( 'wysiwyg-field' );
                    
                    return $field;
                }
    
                return parent::createFieldObject($attr);
        }
    }
    
	function getTransitionAttributes()
	{
		return array();
	}
	
	function getNewObjectAttributes()
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
	    
	    $object = $this->getObject();
	    
	    if ( $this->getDiscriminatorField() != '' )
	    {
	    
	    $discriminatorField = $object->getClassName().$this->getDiscriminatorField();
 		
    	?>
    	<script type="text/javascript">
        var customFields = ['<?=join("','", array_keys($this->customkinds))?>'];
    
        $('#<?=$discriminatorField?>').change( function() {
            jQuery.each(customFields, function(key, value) {
                $('#fieldRow'+value).hide();
            });
            selected = $(this).find('option[value="'+$(this).val()+'"]').attr('referenceName');
            <?php foreach( $this->customkinds as $field => $value ) { ?>
            if ( selected == '<?=$value?>' ) $('#fieldRow<?=$field?>').show();
            <?php } ?>
        });

        $('#<?=$discriminatorField?>').change();
        
    	</script>
    	<?php
    	
	    }
	}

    private $state_it = null;
}
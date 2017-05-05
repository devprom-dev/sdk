<?php

include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class WorkflowTransitionAttributesModelBuilder extends ObjectModelBuilder 
{
	private $transition_it = null;
	private $attributes = array();
    private $data = array();
	
	public function __construct( $transition_it, $attributes = array(), $data = array() )
	{
		$this->transition_it = $transition_it;
		$this->attributes = $attributes;
        $this->data = array_filter($data, function($value) {
            return $value != '';
        });
	}
	
    public function build( Metaobject $object )
    {
    	if ( ! $object instanceof MetaobjectStatable ) return;
 	    if ( $object->getStateClassName() == '' ) return;
        $visibleAttributes = array();
 	    
 	    foreach( $object->getAttributes() as $attribute => $data ) {
 	    	$object->setAttributeVisible($attribute, false);
            $object->setAttributeEditable($attribute, true);
 	    }

 	    if ( $this->transition_it->getId() == '' ) return;

        // allow trace attribute be visible in case of filled value
        $this->data = array_diff_key(
            $this->data,
            array_merge(
                array( 'Tasks' => '', 'Fact' => '' ),
                array_flip($object->getAttributesByGroup('trace'))
            )
        );

        $attribute_it = WorkflowScheme::Instance()->getStateAttributeIt($object, $this->transition_it->get('TargetStateReferenceName'));
		while( !$attribute_it->end() )
		{
            if ( $this->data[$attribute_it->get('ReferenceName')] != '' ) {
                $attribute_it->moveNext();
                continue;
            }
			$object->setAttributeRequired(
				$attribute_it->get('ReferenceName'), $attribute_it->get('IsRequired') == 'Y'
			);
            $object->setAttributeVisible(
                $attribute_it->get('ReferenceName'),
                ($attribute_it->get('IsVisible') == 'Y' || $attribute_it->get('IsRequired') == 'Y') && $attribute_it->get('IsReadonly') != 'Y'
			);
            $object->setAttributeEditable(
                $attribute_it->get('ReferenceName'), $attribute_it->get('IsReadonly') != 'Y'
            );
            if ( $attribute_it->get('IsVisible') == 'Y' ) {
                $visibleAttributes[] = $attribute_it->get('ReferenceName');
            }
			$attribute_it->moveNext();
		}
 	    
 	    $attribute_it = getFactory()->getObject('TransitionAttribute')->getRegistry()->Query(
            array(
                new FilterAttributePredicate('Transition', $this->transition_it->getId())
            )
 	    );

		while( !$attribute_it->end() )
		{
            if ( $attribute_it->get('IsVisible') == 'Y' || $attribute_it->get('IsRequired') == 'Y' ) {
                $object->setAttributeVisible( $attribute_it->get('ReferenceName'), true );
                $object->setAttributeRequired(
                    $attribute_it->get('ReferenceName'), $attribute_it->get('IsRequired') == 'Y'
                );
                $visibleAttributes[] = $attribute_it->get('ReferenceName');
            }
			$attribute_it->moveNext();
		}
		
		if ( $this->transition_it->get('IsReasonRequired') != TransitionReasonTypeRegistry::None ) {
			$object->addAttribute('TransitionComment', 'WYSIWYG', text(1197), true, false,
                str_replace('%1', getFactory()->getObject('Module')->getExact('dicts-texttemplate')->getUrl(), text(606))
            );
            if ( $this->transition_it->get('IsReasonRequired') == TransitionReasonTypeRegistry::Required ) {
                $object->setAttributeRequired('TransitionComment', true);
            }
		}
		
		foreach( $this->attributes as $attribute ) {
			$object->setAttributeVisible($attribute, true);
		}

        foreach( $visibleAttributes as $attribute ) {
            $groups = array_filter($object->getAttributeGroups($attribute), function($value) {
                return !in_array($value, array('trace','additional'));
            });
            if ( is_array($groups) ) $object->setAttributeGroups($attribute, $groups);
        }
    }
}
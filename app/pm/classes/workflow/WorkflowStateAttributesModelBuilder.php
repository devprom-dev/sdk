<?php
include_once SERVER_ROOT_PATH."cms/classes/model/ObjectModelBuilder.php";

class WorkflowStateAttributesModelBuilder extends ObjectModelBuilder 
{
	private $state_it = null;
	private $attributes = array();
	
	public function __construct( $state_it, $attributes = array() )
	{
		$this->state_it = $state_it;
		$this->attributes = $attributes;
	}
	
    public function build( Metaobject $object )
    {
    	if ( ! $object instanceof MetaobjectStatable ) return;
 	    if ( $object->getStateClassName() == '' ) return;
        $visibleAttributes = array();
        $skipAttributes = $object->getAttributesByGroup('system');

 	    if ( count($this->attributes) > 0 )
 	    {
     	    foreach( $object->getAttributes() as $attribute => $data )
	 	    {
	 	    	// skip custom attributes
	 	    	if ( $object->getAttributeOrigin($attribute) == ORIGIN_CUSTOM ) continue;
	 	    	if ( in_array($attribute, $this->attributes) ) continue;
	 	    	
	 	    	$object->setAttributeVisible($attribute, false);
	 	    	$object->setAttributeRequired($attribute, false);
	 	    }
	 	    
 	       	foreach( $this->attributes as $attribute ) {
				$object->setAttributeVisible($attribute, true);
			}
 	    }

		// apply attributes settings for the given state
        $attribute_it = WorkflowScheme::Instance()->getStateAttributeIt(
            $object, $this->state_it->get('ReferenceName'));

		while( !$attribute_it->end() )
		{
            if ( in_array($attribute_it->get('ReferenceName'), $skipAttributes) ) {
                $attribute_it->moveNext();
                continue;
            }

			$object->setAttributeRequired(
					$attribute_it->get('ReferenceName'), $attribute_it->get('IsRequired') == 'Y' 
				);
			$object->setAttributeVisible(
					$attribute_it->get('ReferenceName'), 
					$attribute_it->get('IsVisible') == 'Y' || $attribute_it->get('IsRequired') == 'Y'
				);
            if ( $object->getAttributeEditable($attribute_it->get('ReferenceName')) ) {
                $object->setAttributeEditable(
                    $attribute_it->get('ReferenceName'), $attribute_it->get('IsReadonly') != 'Y'
                );
            }
            if ( $attribute_it->get('IsVisible') == 'Y' ) {
                $object->resetAttributeGroup($attribute_it->get('ReferenceName'), 'form-column-skipped');
            }

            if ( $attribute_it->get('IsMainTab') == 'Y' ) {
                $object->addAttributeGroup($attribute, 'tab-main');
            }
            if ( $attribute_it->get('IsVisibleOnEdit') == 'Y' ) {
                $object->addAttributeGroup($attribute_it->get('ReferenceName'), 'form-column-skipped');
            }
            if ( $attribute_it->get('DefaultValue') != '' ) {
                $object->setAttributeDefault($attribute_it->get('ReferenceName'), $attribute_it->get('DefaultValue'));
            }
			$attribute_it->moveNext();
		}
		$object->addAttribute('TransitionComment', 'WYSIWYG', text(1197), false, false,
            str_replace('%1', getFactory()->getObject('Module')->getExact('dicts-texttemplate')->getUrl(), text(606))
        );
	}
}
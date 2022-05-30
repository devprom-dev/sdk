<?php
include_once "fields/FieldComponentTrace.php";

class ComponentForm extends PMPageForm
{
	function __construct( $object )	{
		parent::__construct( $object );
	}
	
	function extendModel()
    {
        if ( $this->getEditMode() ) {
            $this->getObject()->setAttributeVisible('OrderNum', true);
        }

        parent::extendModel();

        if ( is_object($this->getObjectIt()) ) {
            $this->getObject()->setAttributeVisible('Children', true);
        }
    }

	function createFieldObject( $name )
	{
		switch ( $name ) {
			case 'ParentComponent':
				return new FieldHierarchySelector($this->getObject());

            case 'Children':
                if ( is_object($this->getObjectIt()) ) {
                    return new FieldListOfReferences($this->getObjectIt()->getRef($name));
                }
                return null;

            case 'Requests':
                return new FieldComponentTrace( $this->getObjectIt(),
                    getFactory()->getObject('ComponentTraceRequest') );

            case 'Attachment':
                return new FieldAttachments( is_object($this->getObjectIt()) ? $this->getObjectIt() : $this->object );

			default:
				return parent::createFieldObject( $name );
		}
	}

    function getShortAttributes() {
        return array_merge(
            parent::getShortAttributes(),
            array('Type')
        );
    }

    function getNewRelatedActions()
    {
        $actions = array();

        $method = new ObjectCreateNewWebMethod($this->getObject());
        if ( $method->hasAccess() ) {
            $parms = array(
                'ParentComponent' => $this->getObjectIt()->getId()
            );
            $type_it = getFactory()->getObject('ComponentType')->getAll();
            while( !$type_it->end() ) {
                $actions[] = array(
                    'name' => $type_it->getDisplayName(),
                    'url' => $method->getJSCall(
                                array_merge( $parms,
                                    array('Type' => $type_it->getId())
                                ))
                );
                $type_it->moveNext();
            }
            if ( $type_it->count() < 1 ) {
                $actions[] = array(
                    'name' => $method->getCaption(),
                    'url' => $method->getJSCall($parms)
                );
            }
        }

        return $actions;
    }
}
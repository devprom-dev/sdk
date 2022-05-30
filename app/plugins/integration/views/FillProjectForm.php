<?php

class FillProjectForm extends AjaxForm
{
 	function getAddCaption() {
 		return text('integration24');
 	}
 	
 	function getCommandClass() {
 		return 'fillproject&namespace=integration';
 	}

	function getAttributes()
	{
        $object = $this->getObject();
        foreach( array('Caption', 'Type', 'IsActive', 'Log') as $attribute ) {
            $object->setAttributeVisible($attribute, false);
        }
        $object->setAttributeType('MappingSettings', 'LARGETEXT');

        $app_it = getFactory()->getObject('IntegrationApplication')->getByRef('entityId', $_REQUEST['tracker']);
        $object->setAttributeDefault('Caption', $app_it->getId());

        $object->addAttribute('project', 'VARCHAR', '', false);
        $object->setAttributeDefault('project', getFactory()->getObject('Project')->getByRef('CodeName', $_REQUEST['project'])->getId());

        if ( $app_it->get('ModelBuilder') != '' ) {
            $builderClassName = $app_it->get('ModelBuilder');
            if ( class_exists($builderClassName) ) {
                $builder = new $builderClassName;
                $builder->build($object);
            }
        }

		return parent::getAttributes();
	}

    function getAttributeValue($attribute)
    {
        switch( $attribute ) {
            case 'MappingSettings':
                $app_it = getFactory()->getObject('IntegrationApplication')
                                ->getByRef('entityId', $_REQUEST['tracker']);
                return file_get_contents(SERVER_ROOT_PATH . $app_it->get('ReferenceName'));
        }
        return parent::getAttributeValue($attribute);
    }


    function getButtonText()
	{
		return translate('Продолжить');
	}
}

<?php

class FillProjectForm extends AjaxForm
{
 	function getAddCaption()
 	{
 		return text('integration24');
 	}
 	
 	function getCommandClass()
 	{
 		return 'fillproject&namespace=integration';
 	}

	function getAttributes()
	{
        $object = $this->getObject();
        foreach( array('Caption', 'Type', 'MappingSettings', 'IsActive', 'Log') as $attribute ) {
            $object->setAttributeVisible($attribute, false);
        }

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
	
	function getButtonText()
	{
		return translate('Продолжить');
	}
}

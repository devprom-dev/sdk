<?php
 
class ProfileKeysForm extends AjaxForm
{
    function extendModel()
    {
        parent::extendModel();

        $object = $this->getObject();
        $object->addAttribute('ApiKey', 'VARCHAR', text(2285), true, false,
            sprintf(text(2286), $this->getAttributeValue('ApiKey')), 400);
        $object->setAttributeEditable('ApiKey', false);

        $object->addAttribute('AppKey', 'VARCHAR', text(2544), true, false,
            str_replace('%KEY%', $this->getAttributeValue('AppKey'), text(2545)), 410);
        $object->setAttributeEditable('AppKey', false);

        $visible = array('ApiKey', 'AppKey');
        foreach( $object->getAttributes() as $attribute => $data ) {
            $object->setAttributeVisible($attribute, in_array($attribute, $visible));
        }
    }

    function getModifyCaption() {
 	    return text(2913);
 	}

 	function getCommandClass() {
 		return 'profilemanage';
 	}
 	
 	function getRedirectUrl() {
        return '/keys';
	}

    function getAttributeValue( $attribute ) {
        switch( $attribute ) {
            case 'ApiKey':
                return \AuthenticationAPIKeyFactory::getAuthKey($this->getObjectIt());
            case 'AppKey':
                return \AuthenticationAppKeyFactory::getKey($this->getObjectIt()->getId());
            default:
                return parent::getAttributeValue($attribute);
        }
    }
}

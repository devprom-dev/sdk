<?php
include_once SERVER_ROOT_PATH.'core/methods/ObjectCreateNewWebMethod.php';

class RequestCreateTaskWebMethod extends ObjectCreateNewWebMethod
{
    var $request_it;

    function __construct( $request_it = null )
    {
        parent::__construct( getFactory()->getObject('Task') );

        $this->setRequestIt($request_it);
    }

    function setRequestIt( $request_it )
    {
        $this->request_it = $request_it;
        $this->setVpd($this->request_it->get('VPD'));
    }

    function getCaption()
    {
        return translate('Задача');
    }

    function hasAccess()
    {
        return getSession()->getProjectIt()->getMethodologyIt()->HasTasks()
            && getFactory()->getAccessPolicy()->can_create(getFactory()->getObject('Task'));
    }

    function getJSCall( $parms = array() )
    {
        return parent::getJSCall(
            array_merge( $parms,
                array(
                    'ChangeRequest' => $this->request_it->getId()
                )
            )
        );
    }
}

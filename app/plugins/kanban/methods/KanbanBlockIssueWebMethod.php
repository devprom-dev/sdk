<?php
include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";

class KanbanBlockIssueWebMethod extends WebMethod
{
    private $request_it = null;

    function __construct( $request_it = null )
    {
        parent::__construct();
        $this->setObjectIt($request_it);
        $this->setRedirectUrl('devpromOpts.updateUI');
    }

    function setObjectIt($request_it) {
        $this->request_it = $request_it;
    }

    function getCaption() {
        return translate('Заблокировать');
    }

    function getMethodName() {
        return 'AttributeBlockReason';
    }

    function getJSCall( $parms = array() ) {
        return "javascript:processBulk('".$this->getCaption()."','?formonly=true&operation="
            .$this->getMethodName()."&Comment&project=".$this->request_it->get('ProjectCodeName')."', "
            .$this->request_it->getId().", ".$this->getRedirectUrl().")";
    }

    function hasAccess() {
        return getFactory()->getAccessPolicy()->can_modify($this->request_it);
    }
}
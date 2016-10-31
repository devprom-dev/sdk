<?php
include_once SERVER_ROOT_PATH."core/methods/ModifyAttributeWebMethod.php";

class KanbanUnblockIssueWebMethod extends ModifyAttributeWebMethod
{
    function __construct( $request_it = null )
    {
        parent::__construct($request_it, 'BlockReason', 'NULL');
        $this->setCallback('donothing');
    }

    function getCaption() {
        return translate('Разблокировать');
    }

    function getJSCall( $parms = array() ) {
        return parent::getJSCall(array());
    }
}
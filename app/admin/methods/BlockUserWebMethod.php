<?php
include_once SERVER_ROOT_PATH."core/methods/WebMethod.php";

class BlockUserWebMethod extends WebMethod
{
    function getCaption() {
        return translate('Заблокировать');
    }

    function execute( $parms )
    {
        if ( $this->hasAccess() )
        {
            $list = getFactory()->getObject('BlackList');
            $list_it = $list->getByRef('SystemUser',
                $parms['user']);

            if ( $list_it->count() < 1 )
            {
                $list->add_parms(
                    array (
                        'SystemUser' => $parms['user'],
                        'IPAddress' => '-',
                        'BlockReason' => translate('Пользователь заблокирован администратором')
                    )
                );
            }

            echo '/admin/blacklist.php';
        }
    }

    function hasAccess()
    {
        return getSession()->getUserIt()->IsAdministrator();
    }

    function execute_request()
    {
        global $_REQUEST;
        $this->execute( $_REQUEST );
    }
}
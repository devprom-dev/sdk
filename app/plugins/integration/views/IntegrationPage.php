<?php

include "IntegrationForm.php";
include "IntegrationTable.php";
include "IntegrationSettingsBuilder.php";
        
class IntegrationPage extends PMPage
{
    function __construct()
    {
        getSession()->addBuilder(new IntegrationSettingsBuilder());
        parent::__construct();
    }

    function getObject() {
		return getFactory()->getObject('Integration');
	}
	
    function getTable() {
        return new IntegrationTable($this->getObject());
    }

    function getEntityForm()
    {
        $object = $this->getObject();
        $form = new IntegrationForm($object);
        if ( $this->needDisplayForm() ) {
            $this->setInfoSections(array());
            $this->addInfoSection(new PageSectionAttributes($object, 'mapping', translate('integration6')));
            $this->addInfoSection(new PageSectionAttributes($object, 'additional', translate('Лог')));
        }
        return $form;
    }

    function getRedirect( $renderParms )
    {
        $redirect = parent::getRedirect( $renderParms );
        if ( $redirect != '' ) return $redirect;

        if ( $_REQUEST['integrationlink'] != '' ) {
            $link_it = getFactory()->getObject('IntegrationLink')->getExact(preg_split('/,/', $_REQUEST['integrationlink']));
            if ( $link_it->count() > 0 ) {
                $service = new IntegrationService(
                    getFactory()->getObject('Integration')->getExact($link_it->fieldToArray('Integration')),
                    \Logger::getLogger('Commands')
                );
                $channel = $service->getRemoteChannel();
                $url = $channel->getSearchUrl($link_it->fieldToArray('ExternalId'));
                if ( $url != '' ) {
                    exit(header('Location: '.$url));
                }
            }
        }
    }
}

<?php

class SettingsTableBase extends PMPageTable
{
    function getRenderParms( $parms )
    {
        return array_merge(
            parent::getRenderParms($parms),
            array (
                'navigation_url' => getSession()->getApplicationUrl().'settings',
                'nearest_title' => translate('Настройки')
            )
        );
    }
}
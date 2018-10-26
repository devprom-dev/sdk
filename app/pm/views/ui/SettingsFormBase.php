<?php

class SettingsFormBase extends PMPageForm
{
   function getRenderParms()
    {
        return array_merge(
            parent::getRenderParms(),
            array (
                'uid_icon' => '',
                'uid' => '',
                'navigation_url' => getSession()->getApplicationUrl().'settings',
                'nearest_title' => translate('Настройки')
            )
        );
    }
}
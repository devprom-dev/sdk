<?php

class CheckpointWindowsSMTP extends CheckpointEntryDynamic
{
    function checkWindows()
    {
        global $_SERVER;

        return strpos($_SERVER['OS'], 'Windows') !== false || $_SERVER['WINDIR'] != ''  || $_SERVER['windir'] != '';
    }

    function enabled()
    {
        global $_SERVER;

        if( $this->checkWindows() )
        {
            if ( defined('EMAIL_TRANSPORT') ? EMAIL_TRANSPORT == '1' || EMAIL_TRANSPORT == '' : true )
            {
                return parent::enabled();
            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    function execute()
    {
    	global $model_factory;
    	
    	$class = $model_factory->getClass('MailerSettings');
    	
    	if ( !class_exists($class) ) return;
    	
    	$settings_it = $model_factory->getObject($class)->getAll();
    	
        $this->setValue( in_array($settings_it->get('MailServer'), array('')) ? '0' : '1' );
    }

    function getTitle()
    {
        return 'SMTP: configuration (Windows)';
    }

    function getDescription()
    {
        return str_replace('%1', '/admin/mailer/', text(1232));
    }
}
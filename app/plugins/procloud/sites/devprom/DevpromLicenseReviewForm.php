<?php

class DevpromLicenseReviewForm extends DevpromBaseForm
{
    function getCommandClass()
    {
        return 'reviewlicensekey';
    }

    function getAttributes()
    {
        return array ( 'LicenseKey' );
    }

    function getAttributeType( $attribute )
    {
        switch ( $attribute )
        {
            case 'LicenseKey':
                return 'text';
                
            case 'UserInfo':
            	return 'custom';
        }
    }

    function IsAttributeVisible( $attribute )
    {
        return true;
    }

    function IsAttributeRequired( $attribute )
    {
        return false;
    }

    function getName( $attribute )
    {
        global $model_factory;

        switch ( $attribute )
        {
            case 'LicenseKey':
                return 'Нажмите "Продолжить" для установки ключа:';

            default:
                return parent::getName( $attribute );

        }
    }

    function getAttributeValue( $attribute )
    {
        global $_REQUEST, $model_factory;

        switch ( $attribute )
        {
            case 'LicenseKey':
            	
            	if ( $_REQUEST['LicenseKey'] != '' ) return $_REQUEST['LicenseKey']; 
            	
                $license = $model_factory->getObject('LicenseData');
                 
                $license_it = $license->getAll();
                 
                while ( !$license_it->end() )
                {
                    $check_alt_key = $license_it->get('uid') == $_REQUEST['InstallationUID']
                    && $license_it->get('type') == $_REQUEST['LicenseType'];
                     
                    if ( $check_alt_key )
                    {
                        return $license_it->get('key');
                    }

                    $license_it->moveNext();
                }
                 
            default:
                return parent::getAttributeValue( $attribute );
        }
    }

    function draw()
    {
        global $_REQUEST;

        $form_processor_url = '/command/'.$this->getCommandClass();

        $this->drawScript();

        echo '<style>';
        echo '#myForm table td { padding-bottom:0; border: none; }';
        echo '#myForm table td.value { padding-top:6px; }';
        echo '.required { font-weight: bold; color: red; }';
        echo '.error { color: red; }';
        echo '</style>';

        echo '<div style="width:70%;">';
        echo '<form id="myForm" action="'.$form_processor_url.'" method="post" style="width:100%;" onsubmit="javascript: return false;">';
        echo '<input type="hidden" id="action" name="action" value="'.$this->getAction().'">';
        echo '<input type="hidden" name="MAX_FILE_SIZE" value="1048576">';
        echo '<input type="hidden" id="lru" name="lru" value="">';
        echo '<input type="hidden" id="lrs" name="lrs" value="">';
        echo '<input type="hidden" name="Redirect" value="'.$_REQUEST['Redirect'].'">';
        echo '<input type="hidden" name="InstallationUID" value="'.$_REQUEST['InstallationUID'].'">';
        echo '<input type="hidden" name="LicenseType" value="'.$_REQUEST['LicenseType'].'">';
        
        echo '<table style="width:100%;">';
        $attributes = $this->getAttributes();

        for ( $i = 0; $i < count($attributes); $i++ )
        {
            $this->drawAttribute( $attributes[$i] );
        }
        
	   	$user_attributes = array('UName','UEmail','ULogin','UPassword');
		    	
	   	foreach( $user_attributes as $attribute )
	   	{
			echo '<input type="hidden" name="'.$attribute.'" value="'.htmlentities($_REQUEST[$attribute]).'">';
		}
        
        echo '</table>';
        echo '</form>';
        	
        echo '<div id="result" style="clear:both;padding-bottom:12px;"></div>';

        echo '<div id="frm-buttons" style="width:100%;">';

        $script = "javascript: submitForm('".$this->getAction()."', function() {})";
        	
        echo '<a class="write" style="float:left;" href="'.$script.'">';
        echo 'Продолжить';
        echo '</a>';

        echo '<div id="rt"></div>';
        echo '</div>';

        echo '<div style="clear:both;"></div>';
        echo '</div>';
    }

    function drawTitle()
    {
        
    }
    
    function getSubmitScript()
    {
        return '';
    }
}
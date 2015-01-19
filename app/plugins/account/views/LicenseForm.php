<?php

class LicenseForm extends AjaxForm
{
    function getCommandClass()
    {
        return 'getlicensekey';
    }
    
    function getFormUrl()
    {
    	return '/module/account/command?name='.$this->getCommandClass();
    }

    function getAttributes()
    {
        if ( $_REQUEST['LicenseType'] == 'LicenseTeam' )
        {
            $attributes = array ( 'LicenseType', 'InstallationUID' );
        }
        else
        {
            $attributes = array ( 'LicenseType', 'InstallationUID', 'LicenseValue' );
        }
        
       	switch( $_REQUEST['LicenseType'] )
       	{
			case 'LicenseSAASALM':
			case 'LicenseSAASALMMiddle':
			case 'LicenseSAASALMLarge':
            	
            	$attributes[] = 'Aggreement';
            	
            	break;
       	}
        
        return $attributes;
    }

    function getAttributeType( $attribute )
    {
        switch ( $attribute )
        {
            case 'InstallationUID':
            case 'LicenseType':
            case 'LicenseValue':
                return 'text';

            case 'Aggreement':
                return 'char';
        }
    }

    function IsAttributeVisible( $attribute )
    {
    	switch( $attribute )
    	{
    	    case 'InstallationUID':
    	    	return $this->getAttributeValue($attribute) == '';
    	}
    	
        return true;
    }

    function IsAttributeRequired( $attribute )
    {
        return false;
    }

    function IsAttributeModifable( $attribute )
    {
        return $attribute != 'InstallationUID';
    }
    
    function getName( $attribute )
    {
        switch ( $attribute )
        {
            case 'InstallationUID':
                return text('account2');

            case 'LicenseType':
                return text('account3');

            case 'LicenseValue':
            	switch( $_REQUEST['LicenseType'] )
            	{
            	    case 'LicenseSAASALM':
					case 'LicenseSAASALMMiddle':
					case 'LicenseSAASALMLarge':
            	    	return text('account4');
            	    	
            	    default:
            	    	return text('account5');
            	}
            	
            case 'Aggreement':
            	return text('account6');

            default:
                return parent::getName( $attribute );

        }
    }

    function getAttributeValue( $attribute )
    {
        switch ( $attribute )
        {
            case 'InstallationUID':
            case 'LicenseType':
            case 'LicenseValue':
                if ( $_REQUEST[$attribute] != '' ) return $_REQUEST[$attribute];
                return parent::getAttributeValue( $attribute );

            case 'Aggreement': return 'N';
                
            default:
                return parent::getAttributeValue( $attribute );
        }
    }

    function getDescription( $attribute )
    {
        switch ( $attribute )
        {
            case 'LicenseValue':
                return '&nbsp;';

            default:
                return '';
        }
    }

    function drawCustomAttribute( $attribute, $value, $tab_index )
    {
        global $tab_index;

        switch( $attribute )
        {
            case 'LicenseType':

                $licenses = array (
	                'LicenseEnterprise' => 'Полнофункциональная версия Devprom.ALM',
	                'LicenseTrial' => 'Ознакомительная версия Devprom.ALM',
	                'LicenseTeam' => 'Базовая версия Devprom.AgileTeam',
	                'LicenseSAASALM' => 'Devprom.ALM в облаке myalm.ru на 10 пользователей',
					'LicenseSAASALMMiddle' => 'Devprom.ALM в облаке myalm.ru на 30 пользователей',
					'LicenseSAASALMLarge' => 'Devprom.ALM в облаке myalm.ru на 100 пользователей'
                );

                $type = $this->getAttributeValue( $attribute );

                echo '<div class="input-border form-group" style="">';
	                echo '<input type="hidden" id="LicenseType" name="LicenseType" value="'.$value.'">';
	                echo '<input class="input_value form-control" value="'.$licenses[$value].'" tabindex="'.$tab_index.'" readonly>';
                echo '</div>';

                return;
        }

        return parent::drawCustomAttribute( $attribute, $value, $tab_index );
    }

    function drawScript()
    {
        ?>
	<script language="javascript">
		$(document).ready(function() 
		{
			$('#LicenseType').change(function() {
				window.location = updateLocation('LicenseType='+$('#LicenseType').val(), window.location.toString()); 
			});
		});
	</script>
	<?php 
	}
	
	function draw2()
	{
		$this->drawScript();

		echo '<div style="font-size:10pt;">';
			echo '<br/>';
			echo 'Пользователь: <b>'.getSession()->getUserIt()->getDisplayName().'</b> ('.getSession()->getUserIt()->get('Email').')';
		echo '</div>';
		
		echo '<form id="myForm" action="/module/account/command?name=getlicensekey" method="post" style="width:100%;" onsubmit="javascript: return false;">';
			echo '<input type="hidden" id="action" name="action" value="'.$this->getAction().'">';
			echo '<input type="hidden" name="MAX_FILE_SIZE" value="1048576">';
			echo '<input type="hidden" id="lru" name="lru" value="">';
			echo '<input type="hidden" id="lrs" name="lrs" value="">';
			echo '<input type="hidden" name="Redirect" value="'.htmlentities($_REQUEST['Redirect']).'">';
			echo '<input type="hidden" name="WasLicenseKey" value="'.htmlentities($_REQUEST['LicenseKey']).'">';
			echo '<input type="hidden" name="WasLicenseValue" value="'.htmlentities($_REQUEST['Value']).'">';
			
			echo '<table style="width:100%;">';
			$attributes = $this->getAttributes();
	
			for ( $i = 0; $i < count($attributes); $i++ )
			{
				$this->drawAttribute( $attributes[$i] );
			}
			echo '</table>';
		echo '</form>';
			
		echo '<div style="clear:both;"></div>';
			
       	switch( $_REQUEST['LicenseType'] )
       	{
            	    case 'LicenseSAASALM':
					case 'LicenseSAASALMMiddle':
					case 'LicenseSAASALMLarge':
            	    	
							echo '<br/>';
				           	echo '<span style="font-size:13px;">* Оплата услуг производится <a target="_blank" href="http://devprom.ru/price/Payonline">процессинговым центром PayOnline</a></span>';

				           	break;
       	}
			
		echo '</div>';
	}

	function drawTitle()
	{
	}
	
	function getSubmitScript()
	{
		return '';
	}

	function getTemplate()
	{
		return '../../plugins/account/views/templates/account.tpl.php';
	}
	
	function getRenderParms()
	{
		$parms = parent::getRenderParms();
		
		$parms['buttons_template'] = '../../plugins/account/views/templates/buttons.tpl.php';
		
		return $parms;
	}
}

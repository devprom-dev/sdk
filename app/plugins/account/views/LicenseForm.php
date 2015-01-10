<?php

class LicenseForm extends AjaxForm
{
    function getCommandClass()
    {
        return 'getlicensekey';
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
        global $model_factory;

        switch ( $attribute )
        {
            case 'InstallationUID':
                return 'Ваш идентификатор инсталляции Devprom <span class="required">*</span>';

            case 'LicenseType':
                return 'Выбранный тип лицензии';

            case 'LicenseValue':
            	
            	switch( $_REQUEST['LicenseType'] )
            	{
            	    case 'LicenseSAASALM':
					case 'LicenseSAASALMMiddle':
					case 'LicenseSAASALMLarge':
            	    	return 'Укажите в месяцах продолжительность использования Devprom<span class="required">*</span>';
            	    	
            	    default:
            	    	return 'Укажите количество пользователей <span class="required">*</span>';
            	}
            	
            case 'Aggreement':
            	return 'Я принимаю условия <a href="http://devprom.ru/download/%D0%9B%D0%B8%D1%86%D0%B5%D0%BD%D0%B7%D0%B8%D0%BE%D0%BD%D0%BD%D1%8B%D0%B9-%D0%B4%D0%BE%D0%B3%D0%BE%D0%B2%D0%BE%D1%80-SaaS">договора оказания услуг (лицензионного соглашения)</a>';

            default:
                return parent::getName( $attribute );

        }
    }

    function getAttributeValue( $attribute )
    {
        global $_REQUEST;

        switch ( $attribute )
        {
            case 'InstallationUID':
            case 'LicenseType':
            case 'LicenseValue':

                if ( $_REQUEST[$attribute] != '' )
                {
                    return $_REQUEST[$attribute];
                }

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
	
	function draw()
	{
		global $_REQUEST;
		
		$form_processor_url = '/command/'.$this->getCommandClass();
	
		$this->drawScript();

		echo '<div style="width:70%;font-size:10pt;">';
			echo '<br/>';
			echo 'Пользователь: <b>'.getSession()->getUserIt()->getDisplayName().'</b> ('.getSession()->getUserIt()->get('Email').')';
		echo '</div>';
		
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
			
			echo '<div id="result" style="clear:both;padding-bottom:12px;"></div>';

			echo '<div id="frm-buttons" style="width:100%;">';
				
				$script = "javascript: submitForm('".$this->getAction()."', function() {})";
			
				echo '<a class="write" style="float:left;" href="'.$script.'">';
					echo 'Получить ключ';
				echo '</a>';

				$script = "javascript: submitForm('3', function() {})";
				
				echo '<a style="font-size:13px;float:left;margin-left:18px;margin-top:6px;" href="'.$script.'">';
					echo 'Вернуться';
				echo '</a>';
				
				echo '<div id="rt"></div>';
			echo '</div>';
				
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
}

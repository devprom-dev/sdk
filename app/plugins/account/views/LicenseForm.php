<?php

class LicenseForm extends AjaxForm
{
    function getCommandClass()
    {
        return 'getlicensekey';
    }
    
    function getFormUrl()
    {
    	return ACCOUNT_HOST.'/module/account/command?name='.$this->getCommandClass();
    }

    function getAttributes()
    {
        $product_it = $this->getProduct();
    	$attributes = array();
    	
    	if ( getSession()->getUserIt()->getId() < 1 )
    	{
    		$user_it = getFactory()->getObject('User')->getRegistry()->Query(
					array (
							new FilterAttributePredicate('Email', $this->getAttributeValue('Email'))
					)
			);
   			$attributes = array_merge($attributes, array('ExistPassword'));
    	}
    	else
    	{
    		$attributes = array_merge($attributes, array('UserTitle', 'UserForm'));
    	}
    	
        $attributes = array_merge($attributes, array('LicenseType', 'InstallationUID'));

        if ( $product_it->get('ValueName') != '' ) {
        	$attributes[] = 'LicenseValue';
        }

        $fields = $product_it->get('RequiredFields');
        if ( is_array($fields) ) $attributes = array_merge($attributes, $fields); 
        
        return $attributes;
    }

    function getAttributeType( $attribute )
    {
        switch ( $attribute )
        {
            case 'InstallationUID':
            case 'LicenseValue':
            case 'Options':
            case 'UserName':
            case 'Email':
            	return 'text';

            case 'UserPassword':
            case 'ExistPassword':
            	return 'password';
            	
            case 'Aggreement':
                return 'char';

            case 'LicenseType':
            case 'PaymentServiceInfo':
            case 'UserForm':
            case 'UserTitle':
            case 'AggreementForm':
            	return 'custom';
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
           		return $this->getProduct()->get('ValueName');
            case 'Aggreement':
            	return text('account6');
            case 'PaymentServiceInfo':
            	return '';
            case 'UserName':
            	return text('account14');
            case 'Email':
            	return text('account15');
            case 'UserPassword':
            	return text('account16');
            case 'ExistPassword':
            	return text('account22');
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
            case 'Email':
            case 'UserName':
            	if ( $_REQUEST[$attribute] != '' ) return $_REQUEST[$attribute];
        	    return parent::getAttributeValue( $attribute );

           case 'LicenseValue':
                if ( $_REQUEST[$attribute] != '' ) return $_REQUEST[$attribute];
                return $this->getProduct()->get('ValueDefault'); 
            	
            case 'Aggreement': return 'N';
                
            default:
                return parent::getAttributeValue( $attribute );
        }
    }

    function getDescription( $attribute )
    {
        switch ( $attribute )
        {
            case 'ExistPassword':
            	return str_replace('%1', $this->getId(), text('account23'));
            	
            default:
                return '';
        }
    }

    function drawCustomAttribute( $attribute, $value, $tab_index )
    {
        switch( getSession()->getLanguageUid() ) {
            case 'RU':
                $price_field = 'PriceRUB';
                break;
            case 'EN':
                $price_field = 'PriceUSD';
                break;
        }
        switch( $attribute )
        {
        	case 'LicenseType':
        		$product_it = $this->getProduct()->object->getAll();
				
				echo '<b>'.$this->getName($attribute).'</b>';
				echo '<div/><br/>';
				while( !$product_it->end() ) { 
				?>
				<label class="radio" style="padding-left:">
		  			<input type="radio" name="<?=$attribute?>" value="<?=$product_it->getId()?>" <?=($this->getProduct()->getId() == $product_it->getId() ? 'checked' : '')?> onchange="switchProduct('<?=$product_it->getId()?>');">
		  			<?=$product_it->getDisplayName()?> <?=str_replace('%1', $product_it->get($price_field), text('account34'))?>
				</label>
				<?php
				$product_it->moveNext();
				}
				
				echo '<input type="hidden" name="WasLicenseKey" value="'.htmlspecialchars($_REQUEST['WasLicenseKey']).'">';
				echo '<input type="hidden" name="WasLicenseValue" value="'.htmlspecialchars($_REQUEST['WasLicenseValue']).'">';
				echo '<input type="hidden" name="Redirect" value="'.htmlspecialchars($_REQUEST['Redirect']).'">';
				echo '<input type="hidden" name="Email" value="'.htmlspecialchars($_REQUEST['Email']).'">';
                echo '<input type="hidden" name="LicenseScheme" value="'.htmlspecialchars($_REQUEST['LicenseScheme']).'">';

                if ( is_array($this->getProduct()->get('Options')) ) {
                    echo '<br/>';
                    echo '<b>' . text('account35') . '</b>';
                    echo '<div/><br/>';

                    $product_it->moveFirst();
                    while( !$product_it->end() ) {
                        echo '<div class="options-area" id="Options'.$product_it->getId().'" style="display: '.($this->getProduct()->getId() == $product_it->getId() ? 'block':'none').'">';
                        $options = $product_it->get('Options');
                        foreach ($options as $option_id => $option) {
                            ?>
                            <label class="checkbox" style="padding-left:">
                                <input type="checkbox" class="checkbox" name="<?=$product_it->getId()?>Option_<?= $option['OptionId'] ?>"
                                       checked <?=($option[$price_field] < 1 ? 'disabled' : '')?>>
                                <?=$option['Caption']?>
                                <? if ( $option[$price_field] > 0 ) { ?>
                                <?=str_replace('%1', $option[$price_field], text('account36')) ?>
                                <? } ?>
                            </label>
                            <?php
                        }
                        $product_it->moveNext();
                        echo '</div>';
                    }
                    ?>
                    <script type="text/javascript">
                        function switchProduct(id) {
                            $('.options-area').hide();
                            $('#Options'+id).show();
                        }
                    </script>
                    <?
                }
                echo '<br/>';
                break;

            case 'Options':
                break;

        	case 'PaymentServiceInfo':
        		echo text('account13');
        		break;
        		
        	case 'UserTitle':
        		echo str_replace('%1', getSession()->getUserIt()->getDisplayName(), text('account17'));
        		break;
        		
        	case 'UserForm':
        		echo '<input type="hidden" name="Language" value="'.htmlspecialchars($_REQUEST['Language']).'">';
        		
        	case 'AggreementForm':
        		echo '<hr/>';
        		break;
        		
        	default:
        		return parent::drawCustomAttribute( $attribute, $value, $tab_index );
        }
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

	protected function getProduct()
	{
		$products = array (
			new AccountProduct(),
			new AccountProductSaas(),
			new AccountProductDevOps()
		);

		foreach( $products as $product )
		{
			$iterator = $product->getExact($_REQUEST['LicenseType']);
			if ( $iterator->getId() != '' ) return $iterator;
		}

		return $products[0]->getAll();
	}
}

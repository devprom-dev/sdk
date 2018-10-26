<?php

class FieldUserLicensesAttribute extends Field
{
    private $userIt = null;

	function __construct( $userIt ) {
		$this->userIt = $userIt;
	}

	function draw( $view = null )
	{
        $permissions = array();
        $permissionIt = getFactory()->getObject('LicensePermission')->getAll();

        if ( is_object($this->userIt) ) {
            if ( $this->userIt->get('IsReadonly') == 'N' ) {
                $permissions = $permissionIt->idsToArray();
            }
            elseif ( $this->userIt->get('IsReadonly') != 'Y' ) {
                $permissions = TextUtils::parseItems($this->userIt->get('IsReadonly'));
            }
        }

	    while( !$permissionIt->end() )
        {
            $leftLicenses = $permissionIt->getLeftLicenses();
            $value = $permissionIt->get('IsGlobal') == 'Y' ? 'Y' : (in_array($permissionIt->getId(), $permissions) ? 'Y' : 'N');
            $disabled = $this->readOnly() || $permissionIt->get('IsGlobal') == 'Y' || ($value == 'N' && $leftLicenses < 1);

	        echo '<label class="checkbox">';
                echo '<input type="hidden" name="LicensePermission' . $permissionIt->getId() . 'OnForm" value="' . $value . '">';
                echo '<input class="checkbox" name="LicensePermission' . $permissionIt->getId() . '" type="checkbox" '.($value == 'Y' ? 'checked' : '').' '.($disabled ? 'disabled' : '').'>';
                echo $permissionIt->getDisplayName();
                if ( $permissionIt->get('IsGlobal') == 'N' ) {
                    echo ' ' . str_replace('%1', $leftLicenses, text(2644));
                }
            echo '</label>';
            echo '<input type="hidden" name="IsReadonly" value="'.$this->getEncodedValue().'">';

            $permissionIt->moveNext();
        }
	}

	function getValue()
    {
        if ( !is_object($this->userIt) ) return '';
        return $this->userIt->get('IsReadonly');
    }
}

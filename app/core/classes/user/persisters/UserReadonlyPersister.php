<?php

class UserReadonlyPersister extends ObjectSQLPersister
{
 	function map( &$parms )
	{
        $permissions = array();
        $updatePermissions = false;

        $permissionIt = getFactory()->getObject('LicensePermission')->getAll();
        while( !$permissionIt->end() ) {
            if ( $permissionIt->get('IsGlobal') != 'Y' ) {
                if ( $parms['LicensePermission' . $permissionIt->getId() . 'OnForm'] != '' ) {
                    $updatePermissions = true;
                    $wasPermissions = TextUtils::parseItems($parms['IsReadonly']);

                    $leftLicenses = $permissionIt->getLeftLicenses() + (in_array($permissionIt->getId(), $wasPermissions) ? 1 : 0);
                    if ( $leftLicenses > 0 ) {
                        if ( $parms['LicensePermission' . $permissionIt->getId()] != '' ) {
                            $permissions[] = $permissionIt->getId();
                        }
                    }
                }
            }
            $permissionIt->moveNext();
        }

        if ( $updatePermissions ) {
            $parms['IsReadonly'] = join(',', $permissions);
        }
	}

	function getSelectColumns($alias)
    {
        $allPermissions = join(',',
            array_unique(
                array_map( function($row) {
                    return $row['IsGlobal'] == 'N' ? $row['entityId'] : '';
                },
                getFactory()->getObject('LicensePermission')->getAll()->getRowset()
            )
        ));
        return array (
            " IF(".$alias.".IsReadonly = 'N', '".$allPermissions."', ".$alias.".IsReadonly) IsReadonly "
        );
    }

    function IsPersisterImportant() {
        return true;
    }
}

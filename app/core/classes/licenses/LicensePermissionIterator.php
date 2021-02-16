<?php

class LicensePermissionIterator extends OrderedIterator
{
    function getLeftLicenses()
    {
        $user = getFactory()->getObject('User');
        $user->setAttributeType('IsReadonly', 'varchar');

        $license_it = getFactory()->getObject('LicenseInstalled')->getAll();
        $options = $license_it->getOptions();

        foreach( preg_split('/,/', $options['options']) as $option ) {
            list($permission, $users) = preg_split('/:/', $option);
            if ( $permission == $this->getId() && $users > 0 ) {
                $licensedUsers = $user->getRegistry()->Count(
                    array(
                        new FilterSearchAttributesPredicate($this->getId(), array('IsReadonly')),
                        new UserStatePredicate('nonblocked')
                    )
                );
                $licensedUsers += $user->getRegistry()->Count(
                    array(
                        new FilterAttributePredicate('IsReadonly', 'N'),
                        new UserStatePredicate('nonblocked')
                    )
                );
                return $this->getLicenses() - $licensedUsers;
            }
        }

        $licensedUsers = $user->getRegistry()->Count(
            array(
                new FilterAttributeNotNullPredicate('IsReadonly'),
                new FilterHasNoAttributePredicate('IsReadonly', 'Y'),
                new UserStatePredicate('nonblocked')
            )
        );
        return $this->getLicenses() - $licensedUsers;
    }

    function getLicenses()
    {
        $license_it = getFactory()->getObject('LicenseInstalled')->getAll();
        $options = $license_it->getOptions();
        foreach( preg_split('/,/', $options['options']) as $option ) {
            list($permission, $users) = preg_split('/:/', $option);
            if ( $permission == $this->getId() && $users > 0 ) {
                return $users;
            }
        }
        return $license_it->getUsers();
    }
}

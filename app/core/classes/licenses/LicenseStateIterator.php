<?php

class LicenseStateIterator extends OrderedIterator
{
    function hasPackage($package_name)
    {
        $options = $this->get('Options');
        if ( $options['options'] == '' ) return true;

        foreach( preg_split('/,/', $options['options']) as $option ) {
            list($permission, $users) = preg_split('/:/', $option);
            if ( trim($permission) == trim($package_name) ) return true;
        }

        return false;
    }
}

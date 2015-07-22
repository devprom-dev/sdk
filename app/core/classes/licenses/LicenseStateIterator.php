<?php

class LicenseStateIterator extends OrderedIterator
{
    function hasPackage($package_name)
    {
        $options = $this->get('Options');
        return $options['options'] == '' || in_array($package_name, preg_split('/,\s?/', $options['options']));
    }
}

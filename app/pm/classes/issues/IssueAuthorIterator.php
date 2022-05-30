<?php

class IssueAuthorIterator extends OrderedIterator
{
    function getDisplayNameExt()
    {
        if ( $this->get('CustomerClass') == 'Customer' && $this->get('CustomerId') > 0 ) {
            return $this->getRef('CustomerId', getFactory()->getObject('Customer'))->getDisplayNameExt();
        }
        return parent::getDisplayNameExt();
    }
}

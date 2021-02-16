<?php

class WikiPageBaselineIterator extends OrderedIterator
{
    function getDisplayName()
    {
        return parent::getDisplayName();

        $objectIt = $this->getAnchorIt();
        if ( $objectIt->getId() == '' ) return parent::getDisplayName();

        $uid = new ObjectUID;
        if ( $uid->hasUid($objectIt) ) {
            if ( $this->get('Type') != 'branch' ) {
                $uid->setBaseline($this->getId());
            }
            $title = $uid->getUidWithCaption($objectIt, 20);
        }
        else {
            $title = $objectIt->getDisplayNameExt();
        }

        if ( $this->get('Type') == 'branch' ) {
            $title = translate('Бейзлайн').': '.$title;
        }
        else {
            $title = translate('Версия') . ': ' . parent::getDisplayName() . ' ' . $title;
        }

        return $title;
    }

    function getAnchorIt()
    {
        if ( $this->get('ObjectClass') == '' || $this->get('ObjectId') < 1 ) {
            return $this->object->getEmptyIterator();
        }
        else {
            return getFactory()->getObject($this->get('ObjectClass'))->getExact($this->get('ObjectId'));
        }
    }

    function getUidUrl() {
        return $this->getAnchorIt()->getUidUrl() . '&baseline=' . $this->getId();
    }
}
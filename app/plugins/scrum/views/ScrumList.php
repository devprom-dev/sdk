<?php

class ScrumList extends PMPageList
{
    function extendModel()
    {
        parent::extendModel();
        $this->getObject()->addAttribute('GroupDate', 'DATE', translate('Дата'), true);
            foreach( array('RecentComment', 'Participant') as $attribute ) {
            $this->getObject()->setAttributeVisible($attribute, true);
        }
    }

	function getGroupDefault() {
		return 'GroupDate';
	}
}